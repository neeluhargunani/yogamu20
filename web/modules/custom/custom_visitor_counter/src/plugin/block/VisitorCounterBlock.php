<?php

namespace Drupal\custom_visitor_counter\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Visitor Counter' block.
 *
 * @Block(
 *   id = "visitor_counter_block",
 *   admin_label = @Translation("Visitor Counter"),
 *   category = @Translation("Custom")
 * )
 */
class VisitorCounterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // Function to update counters
    function updateCounters() {
      $currentDate = date('Y-m-d');

      // Retrieve counters from the database
      $counters = \Drupal::database()->select('custom_visitor_counters', 'c')
        ->fields('c')
        ->condition('date', $currentDate)
        ->execute()
        ->fetchAssoc();

      // Initialize counters if not present
      if (empty($counters)) {
        $counters = [
          'date' => $currentDate,
          'today' => 0,
          'yesterday' => 0,
          'weekly' => 0,
          'monthly' => 0,
          'total' => 0,
        ];
        \Drupal::database()->insert('custom_visitor_counters')->fields($counters)->execute();
      }

      // Update counters
      \Drupal::database()->update('custom_visitor_counters')
        ->condition('date', $currentDate)
        ->expression('today', 'today + 1')
        ->expression('total', ' monthly + today')
        ->execute();

// Update yesterday counter
// Update yesterday counter
// Find the last recorded visit date before the current date
$lastVisitDate = \Drupal::database()->select('custom_visitor_counters', 'c')
  ->fields('c', ['date'])
  ->condition('date', $currentDate, '<')
  ->orderBy('date', 'DESC')
  ->range(0, 1)
  ->execute()
  ->fetchField();

if ($lastVisitDate !== FALSE) {
  // Fetch the visit count for the last recorded visit date
  $yesterdayVisits = \Drupal::database()->select('custom_visitor_counters', 'c')
    ->fields('c', ['today'])
    ->condition('date', $lastVisitDate)
    ->execute()
    ->fetchField();
  
  // Check if visit count for yesterday is found
  if ($yesterdayVisits !== FALSE) {
    // Records found for yesterday, use the fetched value
    $yesterdayVisits = intval($yesterdayVisits);
  } else {
    // No records found for yesterday, set the count to 0
    $yesterdayVisits = 0;
  }
} else {
  // No previous visit recorded, set yesterday's visit count to 0
  $yesterdayVisits = 0;
}

// Update yesterday's visit count in the database
\Drupal::database()->update('custom_visitor_counters')
  ->condition('date', $currentDate)
  ->fields(['yesterday' => $yesterdayVisits])
  ->execute();

// Update monthly counter
$monthStart = strtotime('first day of this month');
$monthStartDate = date('Y-m-d', $monthStart);
$monthlyVisits = \Drupal::database()->select('custom_visitor_counters', 'c')
  ->fields('c')
  ->condition('date', $monthStartDate, '>=')
  ->condition('date', $currentDate, '<=')
  ->execute()
  ->fetchAll();

// Calculate the sum of yesterday, today, weekly, and monthly for the past 30 days
$monthlySum = 0;
foreach ($monthlyVisits as $monthlyVisit) {
  $monthlySum += $monthlyVisit->yesterday + $monthlyVisit->today + $monthlyVisit->weekly + $monthlyVisit->monthly;
}



      // Update weekly counter
$weekStartDate = date('Y-m-d', strtotime('-7 days'));
$weeklyVisits = \Drupal::database()->select('custom_visitor_counters', 'c')
  ->fields('c', ['yesterday', 'today'])
  ->condition('date', $weekStartDate, '>=')
  ->condition('date', $currentDate, '<=')
  ->execute()
  ->fetchAll();

// Calculate the sum of yesterday and today for the past 7 days
$weeklySum = 0;
foreach ($weeklyVisits as $weeklyVisit) {
  $weeklySum += $weeklyVisit->yesterday + $weeklyVisit->today;
}

// Update weekly counter with the calculated sum
\Drupal::database()->update('custom_visitor_counters')
  ->condition('date', $currentDate)
  ->fields(['weekly' => $weeklySum])
  ->execute();

// Update monthly counter
$monthStartDate = date('Y-m-d', strtotime('-30 days'));
$monthlyVisits = \Drupal::database()->select('custom_visitor_counters', 'c')
  ->fields('c', ['yesterday', 'today'])
  ->condition('date', $monthStartDate, '>=')
  ->condition('date', $currentDate, '<=')
  ->execute()
  ->fetchAll();

// Calculate the sum of yesterday and today for the past 30 days
$monthlySum = 0;
foreach ($monthlyVisits as $monthlyVisit) {
  $monthlySum += $monthlyVisit->yesterday + $monthlyVisit->today;
}

// Update monthly counter with the calculated sum
\Drupal::database()->update('custom_visitor_counters')
  ->condition('date', $currentDate)
  ->fields(['monthly' => $monthlySum])
  ->execute();

      // Return counters for display
      return $counters;
    }

    // Fetch counters
    $counters = updateCounters();

    // HTML markup
    $build['#markup'] = "
      <div class='row'>
        <div class='col-md-12'>
          <div class='website-counter'>
            <div class='col-md-12 d-flex'>
              <div class='col-md-6'>
                <div class='card'>
                  <div class='card-body'>
                    <h5 class='card-title'>
                      <span class='v_ico'><i class='fas fa-tachometer-alt'></i></span>
                      <span class='v_title'>Total Hits</span>
                    </h5>
                    <p class='card-text v_value' id='totalHits'>{$counters['total']}</p>
                  </div>
                </div>
              </div>

              <div class='col-md-6'>
                <div class='card'>
                  <div class='card-body'>
                    <h5 class='card-title'>
                      <span class='v_ico'><i class='fas fa-calendar-day'></i></span>
                      <span class='v_title'>Today Visited</span>
                    </h5>
                    <p class='card-text v_value' id='todayCount'>{$counters['today']}</p>
                  </div>
                </div>
              </div>
            </div><!---top md-->
            <div class='col-md-12 d-flex'>
              <div class='col-md-4'>
                <div class='card'>
                  <div class='card-body'>
                    <h5 class='card-title'>
                      <span class='v_ico'><i class='fas fa-chart-line'></i></span>
                      <span class='v_title'>Yesterday Visited</span>
                    </h5>
                    <p class='card-text v_value' id='yesterdayCount'>{$counters['yesterday']}</p>
                  </div>
                </div>
              </div>

              <div class='col-md-4'>
                <div class='card'>
                  <div class='card-body'>
                    <h5 class='card-title'>
                      <span class='v_ico'><i class='fas fa-chart-bar'></i></span>
                      <span class='v_title'>Weekly Visited</span>
                    </h5>
                    <p class='card-text v_value' id='weeklyCount'>{$counters['weekly']}</p>
                  </div>
                </div>
              </div>

              <div class='col-md-4'>
                <div class='card'>
                  <div class='card-body'>
                    <h5 class='card-title'>
                      <span class='v_ico'><i class='fas fa-globe'></i></span>
                      <span class='v_title'>Monthly Visited</span>
                    </h5>
                    <p class='card-text v_value' id='monthlyCount'>{$counters['monthly']}</p>
                  </div>
                </div>
              </div>
            </div><!--end of md-bottom-->
          </div><!-- end of website counter -->
        </div><!-- end of col-md-12 -->
      </div><!-- end of main row -->
    ";

    return $build;
  }

}
