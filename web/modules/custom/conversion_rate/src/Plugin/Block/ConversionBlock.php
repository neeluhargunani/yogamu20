<?php

namespace Drupal\conversion_rate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "coversion_block",
 *   admin_label = @Translation("Conversion block"),
 *   category = @Translation("Conversion Block"),
 * )
 */
class ConversionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $database = \Drupal::database();
    $query = $database->select('commerce_order', 'cit');
    $query->fields('cit', [
        'order_id',
      ]);
      $query->condition('state', 'completed','=');
      $order_result = $query->execute();
      $count = 0;        
      foreach ($order_result as $result){
                 $count++;
              } 
    $dquery = $database->select('commerce_order', 'cit');
    $dquery->fields('cit', [
        'order_id',
    ]);
    $dquery->condition('state', 'draft','=');
    $order_result = $dquery->execute();
    $dcount = 0;        
    foreach ($order_result as $result){
                $dcount++;
            }           
            
    $nquery = $database->select('node_counter', 'nc');
    $nquery->fields('nc', [
        'totalcount',
    ]);
    $nquery->addExpression('SUM(totalcount)');
    $view_result = $nquery->execute();
     foreach($view_result as $count_result){
        $visitor = $count_result->expression;
     }
    //  $html = '<table class="table table-border"><thead><tr><th>Placed Order</th><th>Site Visitors</th><th>Conversion Rate</th></tr></thead>';
    //  $html .= '<tr><td>'. $count . '</td><td>' . $visitor . '</td><td>' . number_format((float)$count/$visitor*100, 2, '.', '') . '%</td></tr>';
    //  $html .= '<tr><td><b>(Cart)</b>'. $dcount . '</td><td>' . $visitor . '</td><td>' . number_format((float)$dcount/$visitor*100, 2, '.', '') . '%</td></tr>';
    //  $html .= '</table>';

    // return [
    //   '#markup' => $html,
    // ];
  
      $build = [];
      $build['#markup'] = '<div class="row">
       <div class="col-md-12 ">  
       <div class=" conver_rate ">
      <div class="col-md-12 col-xl-12 ">
      <div class="card ">
      <div class="card-title"><h5>MyEstore Conversion Rates</h5></div>
      <div class="card-body responsive">
  
              <table class="table">
             <tr> <div class=" c_content"><th><span class="c_ico"><i class="fas fa-shopping-cart"></i></span><span class="c_title">New Cart</span></th> <td><span class="c-value">'. $dcount . '</span><td></div><tr>
             <tr> <h6 class=" "><th><span class="c_ico"><i class="fas fa-users" ></i></span><span class="c_title">Site Visitors</span></th> <td> <span class="c-value">' . number_format((float)$dcount/$visitor*100, 2, '.', '') . '%</span><td></h6><tr>
             <tr> <h6 class=" "><th><span class="c_ico"><i class="fas fa-users" ></i></span><span class="c_title">Total Placed Order</span ></th> <td> <span class="c-value">'. $count . '</span><td></h6><tr>
            </table>
</div>
         
      </div>
  </div>

   </div></div> </div>';
      return $build;

  
  }

}