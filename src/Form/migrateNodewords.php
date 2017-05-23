<?php

/**
 * @file
 * Contains \Drupal\exclude_bundles\Form\SearchBundles.
 */

namespace Drupal\migrate_nodewords\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

/**
 * Class SearchBundles.
 *
 * @package Drupal\exclude_bundles\Form
 */
class migrateNodewords extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'migrate_nodewords';
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
        'migrate_nodewords.settings',
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = array();
    $form['description'] = array(
        '#type' => 'item',
        '#title' => t('Description'),
        '#markup' => "We are going to migrate Nodewords from D6",
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $will = 1;
    $this->migrate_nodewords();
    parent::submitForm($form, $form_state);
  }
  
 public function migrate_nodewords() {
    Database::setActiveConnection('drupal_6');
    $db = \Drupal\Core\Database\Database::getConnection();
    
    //First get all the nids we have in nodewords
    $query = $db->select('nodewords','n');
    $query->groupBy('n.id');
    $query->fields('n', ['id']);
    $query->condition('n.id', 0, '>');
    $results = $query->execute();
    
    //for each one get all the values
    foreach ($results as $result) {
      $nid = $result->id;
      $subquery = $db->select('nodewords','n')->fields('n', ['name', 'content'])->condition('n.id', $nid)->execute();
      foreach($subquery as $metatag) {
        $metaarray[$nid][$metatag->name] = unserialize($metatag->content)['value'];
      }
      //Now get the page title
      $titlequery = $db->select('page_title','p')->fields('p', ['page_title'])->condition('p.id', $nid)->execute();
      $metaarray[$nid]['title'] = $titlequery->fetchField();
    }
    
    Database::setActiveConnection();
    
    foreach($metaarray as $nid => $meta) {
      $d8node = Node::load($nid);
      if($d8node) {
        $d8node->field_metatags->value = serialize($meta);
        $d8node->path->pathauto = 0; //stops aliases being generated
        $save = $d8node->save();
      }
    }
    
    
  }

}
