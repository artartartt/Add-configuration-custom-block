<?php

namespace Drupal\configure_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'Fax' block.
 *
 * @Block(
 *   id = "fax_block",
 *   admin_label = @Translation("Fax block"),
 * )
 */
class FaxBlock extends BlockBase
{


  /**
   * {@inheritdoc}
   */
  public function build()
  {

    foreach ($this->configuration['block_name'] as $key => $value) {
      //add link in elements
      if (isset($value['name']) && isset($value['link'])) {
        $value['link'] = "https://" . $value['link'];
        $url = Url::fromUri("" . $value['link']);
        $link = Link::fromTextAndUrl($value['name'], $url);
        $list[] = $link;
      }
    }
    $output['links'] = [
      '#theme' => 'item_list',
      '#items' => $list,
      '#title' => $this->t('Media Links'),
    ];
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $num_names = $form_state->get('num_names');
    // We have to ensure that there is at least one name field.

    if ($num_names === NULL ) {
      if(count($this->configuration['block_name']) < 1){
        $name_field = $form_state->set('num_names', 1);
        $num_names = 1;
        $form_state->setRebuild();
      }
      else{
        $name_field = $form_state->set('num_names', count($this->configuration['block_name']));
        $num_names = count($this->configuration['block_name']);
        $form_state->setRebuild();
      }

    }

//linki xndira &
//    dump(count($this->configuration['block_name']));

    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Site name'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_names; $i++) {

      $form['names_fieldset'][$i] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Site ') . ' ' . ($i + 1),
      ];

      $form['names_fieldset'][$i]['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
        '#default_value' => $this->configuration['block_name'][$i]['name'],
      ];

      $form['names_fieldset'][$i]['link'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link'),
        '#default_value' => $this->configuration['block_name'][$i]['link'],

      ];
    }

    $form['names_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $form['names_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add '),
      '#submit' => [[$this, 'addOneBlock']],
      '#ajax' => [
        'callback' => [$this, 'addmoreSiteCallback'],
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];
    // If there is more than one name, add the remove button.
    if ($num_names > 1) {
        $form['names_fieldset']['actions']['remove_name'] = [
          '#type' => 'submit',
          '#value' => $this->t('Remove '),
          '#submit' => [[$this, 'removeCallback']],
          '#ajax' => [
            'callback' => [$this, 'addmoreSiteCallback'],
            'wrapper' => 'names-fieldset-wrapper',
          ],
        ];
    }
//    dump($form);
//    dump($form_state->get('num_names'));

    // inq@ stex jista cuyc talis het ovor hert@ hasnuma funkciain inq@ chi asxatum
    // kamel petqa inchvor dev click anenq et btn-i vra etqan angam

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();
    $this->configuration['block_name'] = $values['names_fieldset'];
  }


  // add new block
  public function addOneBlock(array &$form, FormStateInterface $form_state)
  {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Callback method for ...
   * @param array $form
   * @param FormStateInterface $form_state
   * @return mixed
   */
  public function addmoreSiteCallback(array &$form, FormStateInterface $form_state)
  {
    return $form['settings']['names_fieldset'];
  }

  //remove block if block count > 1
  public function removeCallback(array &$form, FormStateInterface $form_state)
  {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    $form_state->setRebuild();
  }

}
