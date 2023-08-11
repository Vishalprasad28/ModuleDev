<?php

namespace Drupal\flagship_programs\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Contains the function to render the flagfship program data.
 */
class FlagshipProgramView extends ControllerBase {

  /**
   * Function to render the config form data that has been taken input.
   *
   *   Form used to process data is,
   *
   *   @see \Drupal\flagship_programs\Form\FlagshipProgramsForm
   *
   * @return array
   *   Returns the render array.
   */
  public function viewFlagshipData() {
    return [
      '#type' => 'markup',
    ];
  }

  /**
   * A Function to render a page with a webform and a paragraph in 50-50 width.
   *
   * @return array
   *   Returns the render array,
   */
  public function showWebform() {
    return [
      '#type' => 'theme',
      '#theme' => 'webform_paragraph_view',
      '#attached' => [
        'library' => ['simplesmart/layout-css'],
      ],
    ];
  }

}
