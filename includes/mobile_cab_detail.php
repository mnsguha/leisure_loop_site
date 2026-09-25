<?php
// includes/mobile_cab_detail.php
// Backend Rule #16: Dispatched only from cab-detail.php when $is_mobile === true.
// All DB reads completed in controller. No queries here.

$vehicles = $vehicles ?? [];
$all_vehicle_rates = $all_vehicle_rates ?? [];
$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// Hero + sticky summary (no shared app header on this view)
if ($is_search) {
    $summary_trip_title = $s_type === 'hourly'
        ? 'Hourly Rental'
        : ($s_type === 'itinerary' ? 'Itinerary Wise' : 'OutStation One-Way');
} else {
    $summary_trip_title = !empty($cab_class['name']) ? $cab_class['name'] : 'Cabs';
}
$back_url        = 'cabs.php?view=mobile';
$hide_bottom_nav = true;

$service_label = $display_service_options ?? 'Oneway';

$scheduled_line = trim(
    'SCHEDULED '
    . ($s_date ? strtoupper(date('D, d M Y', strtotime($s_date))) : 'TBD')
    . ($s_time ? ' ' . strtoupper(date('h:i A', strtotime($s_time))) : '')
);

// Hero slides from cab_classes.image (admin/cab_classes.php); selected class first in detail mode
$cab_classes  = $cab_classes ?? [];
$hero_fallback = 'assets/img/pkg.jpg';
$hero_slides  = [];
$seen_class   = [];

$push_class_slide = static function (array $cc) use (&$hero_slides, &$seen_class, $hero_fallback): void {
    $cid = (int)($cc['id'] ?? 0);
    if ($cid <= 0 || isset($seen_class[$cid])) {
        return;
    }
    $seen_class[$cid] = true;
    $raw = (string)($cc['image'] ?? '');
    if ($raw !== '') {
        $src = preg_match('/^https?:\/\//i', $raw) ? $raw : ltrim($raw, '/');
    } else {
        $src = $hero_fallback;
    }
    $hero_slides[] = [
        'src'  => $src,
        'name' => (string)($cc['name'] ?? ''),
    ];
};

if (!$is_search && !empty($cab_class)) {
    $push_class_slide($cab_class);
}
foreach ($cab_classes as $cc) {
    $push_class_slide($cc);
}
if (empty($hero_slides)) {
    $hero_slides[] = ['src' => $hero_fallback, 'name' => 'Premium Fleet'];
}
$hero_slides = array_slice($hero_slides, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo htmlspecialchars($page_title ?? 'Cab Results | Leisure Loop'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/mobile-views.css?v=<?php echo time(); ?>">
</head>
<body class="m-hotels-body m-cab-results-body">

    <!-- Hero: auto-scroll cab class images; trip title is the page h1 -->
    <section class="m-cab-hero-section" aria-label="Trip overview">
        <a href="<?php echo htmlspecialchars($back_url); ?>" class="m-cab-hero-back" aria-label="Go back to cabs">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
        </a>

        <div class="m-cab-hero-slider" id="mCabsHeroSlider">
            <?php foreach ($hero_slides as $idx => $slide): ?>
            <div class="m-cab-hero-slide" data-slide-idx="<?php echo (int)$idx; ?>">
                <img src="<?php echo htmlspecialchars($slide['src']); ?>" class="m-cab-hero-img"
                    alt="" aria-hidden="true"
                    loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">
            </div>
            <?php endforeach; ?>
        </div>

        <div class="m-cab-hero-overlay" aria-hidden="true"></div>
        <div class="m-cab-hero-content">
            <h1 class="m-cab-hero-title"><?php echo htmlspecialchars($summary_trip_title); ?></h1>
        </div>

        <?php if (count($hero_slides) > 1): ?>
        <div class="m-cabs-hero-dots" id="mCabsHeroDots" aria-hidden="true">
            <?php foreach ($hero_slides as $idx => $slide): ?>
            <span class="m-cabs-hero-dot<?php echo $idx === 0 ? ' is-active' : ''; ?>" data-dot-idx="<?php echo (int)$idx; ?>"></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- Sticky summary: schedule + route + edit -->
    <section class="m-cab-sticky-summary-bar" aria-label="Search summary">
        <?php if ($is_search): ?>
        <div class="m-cab-sticky-row">
            <div class="m-cab-sticky-schedule">
                <span class="material-symbols-outlined" aria-hidden="true">event</span>
                <span><?php echo htmlspecialchars($scheduled_line); ?></span>
            </div>
            <button type="button" class="m-cab-edit-search-btn" id="mCabEditSearchBtn"
                aria-label="Edit search" aria-controls="mCabSearchSectionWrapper" aria-expanded="false">
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
            </button>
        </div>
        <div class="m-cab-route-grid">
            <div class="m-cab-route-cell">
                <span class="m-cab-route-label">From</span>
                <span class="m-cab-route-value"><?php echo htmlspecialchars($s_pickup ?: 'N/A'); ?></span>
            </div>
            <div class="m-cab-route-cell">
                <span class="m-cab-route-label">
                    <?php
                    if ($s_type === 'hourly') {
                        echo 'Duration';
                    } elseif ($s_type === 'itinerary') {
                        echo 'Itinerary';
                    } else {
                        echo 'To';
                    }
                    ?>
                </span>
                <span class="m-cab-route-value">
                    <?php
                    if ($s_type === 'hourly') {
                        echo htmlspecialchars($s_duration ? $s_duration . ' Hours' : 'N/A');
                    } elseif ($s_type === 'itinerary') {
                        echo htmlspecialchars($s_itinerary ?: 'N/A');
                    } else {
                        echo htmlspecialchars($s_drop ?: 'N/A');
                    }
                    ?>
                </span>
            </div>
            <?php if ($s_type === 'itinerary' && $s_drop_date !== ''): ?>
            <div class="m-cab-route-cell m-cab-route-cell--full">
                <span class="m-cab-route-label">Return</span>
                <span class="m-cab-route-value"><?php echo htmlspecialchars(strtoupper(date('D, d M Y', strtotime($s_drop_date)))); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="m-cab-sticky-row">
            <p class="m-cab-summary-hint">Tap the edit icon to refine your search.</p>
            <button type="button" class="m-cab-edit-search-btn" id="mCabEditSearchBtn"
                aria-label="Edit search" aria-controls="mCabSearchSectionWrapper" aria-expanded="false">
                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
            </button>
        </div>
        <?php endif; ?>
    </section>

    <main class="content-area m-cab-detail-main">

        <!-- Hidden rates store (Rule 11: unique mobile ID) -->
        <div id="m-cab-rates-data-store" hidden data-rates="<?php echo htmlspecialchars(json_encode($all_vehicle_rates), ENT_QUOTES, 'UTF-8'); ?>"></div>

        <!-- Search (Expandable form) -->
        <div id="mCabSearchSectionWrapper" class="is-hidden">
        <section class="m-cabs-search-section">
            <div class="m-cab-search-tabs m-cab-search-tabs--in-card" role="tablist" aria-label="Cab service type">
                <button type="button" class="m-cab-tab" role="tab" aria-selected="<?php echo $s_type === 'oneway' ? 'true' : 'false'; ?>" aria-controls="mCabSearchCard" id="mCabTabOneway" data-type="oneway" tabindex="<?php echo $s_type === 'oneway' ? '0' : '-1'; ?>">Oneway</button>
                <button type="button" class="m-cab-tab" role="tab" aria-selected="<?php echo $s_type === 'hourly' ? 'true' : 'false'; ?>" aria-controls="mCabSearchCard" id="mCabTabHourly" data-type="hourly" tabindex="<?php echo $s_type === 'hourly' ? '0' : '-1'; ?>">Hourly</button>
                <button type="button" class="m-cab-tab" role="tab" aria-selected="<?php echo $s_type === 'itinerary' ? 'true' : 'false'; ?>" aria-controls="mCabSearchCard" id="mCabTabItinerary" data-type="itinerary" tabindex="<?php echo $s_type === 'itinerary' ? '0' : '-1'; ?>">Itinerary</button>
            </div>

            <form action="cab-detail.php" method="GET" class="m-cab-search-card m-hotel-search-card" id="mCabSearchCard" role="tabpanel" aria-labelledby="mCabTab<?php echo $s_type === 'hourly' ? 'Hourly' : ($s_type === 'itinerary' ? 'Itinerary' : 'Oneway'); ?>">
                <input type="hidden" name="search" value="1">
                <input type="hidden" name="type" id="mCabSearchType" value="<?php echo htmlspecialchars($s_type); ?>">
                <input type="hidden" name="view" value="mobile">

                <div class="m-search-field-row" id="mCabRowPickup">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">location_on</span>
                    <div class="m-search-field-content">
                        <label for="mCabPickup" class="m-search-kicker">PICKUP LOCATION</label>
                        <input type="text" id="mCabPickup" name="pickup_location" class="m-search-input" placeholder="Enter pickup city or area" autocomplete="off" value="<?php echo htmlspecialchars($s_pickup); ?>" required>
                    </div>
                </div>

                <div class="m-search-field-row<?php echo $s_type !== 'oneway' ? ' is-hidden' : ''; ?>" id="mCabRowDrop">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">location_on</span>
                    <div class="m-search-field-content">
                        <label for="mCabDrop" class="m-search-kicker">DROP LOCATION</label>
                        <input type="text" id="mCabDrop" name="drop_location" class="m-search-input" placeholder="Enter drop city or area" autocomplete="off" value="<?php echo htmlspecialchars($s_drop); ?>"<?php echo $s_type !== 'oneway' ? ' disabled' : ''; ?>>
                    </div>
                </div>

                <div class="m-search-field-row<?php echo $s_type !== 'itinerary' ? ' is-hidden' : ''; ?>" id="mCabRowItinerary">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">route</span>
                    <div class="m-search-field-content">
                        <label for="mCabItinerary" class="m-search-kicker">ITINERARY / ROUTE DETAILS</label>
                        <input type="text" id="mCabItinerary" name="itinerary_details" class="m-search-input" placeholder="e.g. Delhi → Agra → Jaipur → Delhi" value="<?php echo htmlspecialchars($s_itinerary); ?>"<?php echo $s_type !== 'itinerary' ? ' disabled' : ''; ?>>
                    </div>
                </div>

                <div class="m-cab-dates-row">
                    <div class="m-search-field-row m-cab-date-field" id="mCabRowTravelDate">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">calendar_month</span>
                        <div class="m-search-field-content">
                            <label for="mCabTravelDate" class="m-search-kicker">PICKUP DATE</label>
                            <input type="date" id="mCabTravelDate" name="travel_date" class="m-search-input" min="<?php echo htmlspecialchars($today_date); ?>" value="<?php echo htmlspecialchars($s_date); ?>" required>
                        </div>
                    </div>

                    <div class="m-search-field-row m-cab-date-field<?php echo $s_type !== 'itinerary' ? ' is-hidden' : ''; ?>" id="mCabRowReturnDate">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">calendar_month</span>
                        <div class="m-search-field-content">
                            <label for="mCabReturnDate" class="m-search-kicker">RETURN DATE</label>
                            <input type="date" id="mCabReturnDate" name="return_date" class="m-search-input" min="<?php echo htmlspecialchars($s_date !== '' ? $s_date : $today_date); ?>" value="<?php echo htmlspecialchars($s_drop_date); ?>"<?php echo $s_type !== 'itinerary' ? ' disabled' : ''; ?>>
                        </div>
                    </div>

                    <div class="m-search-field-row m-cab-date-field" id="mCabRowTravelTime">
                        <span class="material-symbols-outlined m-search-icon" aria-hidden="true">schedule</span>
                        <div class="m-search-field-content">
                            <label for="mCabTravelTime" class="m-search-kicker">TIME</label>
                            <input type="time" id="mCabTravelTime" name="travel_time" class="m-search-input" value="<?php echo htmlspecialchars($s_time); ?>">
                        </div>
                    </div>
                </div>

                <div class="m-search-field-row<?php echo $s_type !== 'hourly' ? ' is-hidden' : ''; ?>" id="mCabRowDuration">
                    <span class="material-symbols-outlined m-search-icon" aria-hidden="true">hourglass_top</span>
                    <div class="m-search-field-content">
                        <label for="mCabDuration" class="m-search-kicker">RENT FOR</label>
                        <select id="mCabDuration" name="duration" class="m-search-input"<?php echo $s_type !== 'hourly' ? ' disabled' : ''; ?>>
                            <option value="">Select duration</option>
                            <?php if (!empty($cab_durations)): foreach ($cab_durations as $cd): ?>
                            <option value="<?php echo htmlspecialchars((string)$cd['hours']); ?>"<?php echo $s_duration === (string)$cd['hours'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($cd['label']); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primary m-hotel-search-btn">SEARCH CABS</button>
            </form>
        </section>
        </div>
        <?php if (!$is_search && !empty($cab_class)): ?>
        <!-- Class header (detail mode) -->
        <section class="m-cab-class-card">
            <h2 class="m-cab-class-name"><?php echo htmlspecialchars($cab_class['name']); ?></h2>
            <?php if (!empty($cab_class['description'])): ?>
            <p class="m-cab-class-desc"><?php echo htmlspecialchars($cab_class['description']); ?></p>
            <?php endif; ?>
            <?php if ($class_min_rate): ?>
            <p class="m-cab-class-rate">Starting from &#8377;<?php echo number_format((float)$class_min_rate); ?> / day</p>
            <?php else: ?>
            <p class="m-cab-class-rate">Contact for pricing</p>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- Vehicle results -->
        <section class="m-cab-results" aria-label="Available vehicles">
            <?php if (empty($vehicles)): ?>
            <p class="m-cab-empty">No vehicles available for this search right now.</p>
            <?php else: ?>
                <?php foreach ($vehicles as $v): ?>
                <?php
                    $display_rate = $v['display_rate'] ?? null;
                    $raw_img = (string)($v['image'] ?? '');
                    if ($raw_img !== '' && preg_match('/^https?:\/\//i', $raw_img)) {
                        $v_img = $raw_img;
                    } elseif ($raw_img !== '') {
                        $v_img = ltrim($raw_img, '/');
                    } else {
                        $v_img = 'assets/img/pkg.jpg';
                    }
                    $v_alt = htmlspecialchars($v['name'] ?? 'Vehicle', ENT_QUOTES);
                ?>
                <article class="m-cab-result-card" data-vid="<?php echo (int)$v['id']; ?>" data-baseprice="<?php echo $display_rate !== null ? htmlspecialchars((string)$display_rate) : 0; ?>" id="m-vehicle-card-<?php echo (int)$v['id']; ?>">
                    <div class="m-cab-result-head">
                        <div class="m-cab-result-img-wrapper">
                            <img src="<?php echo htmlspecialchars($v_img); ?>" alt="<?php echo $v_alt; ?>" class="m-cab-result-img" loading="lazy">
                        </div>
                        <div class="m-cab-result-title-specs">
                            <h3 class="m-cab-result-name"><?php echo $v_alt; ?></h3>
                            <div class="m-cab-result-specs">
                                <?php echo (int)($v['pax_capacity'] ?? 0); ?> Seat | <?php echo (int)($v['luggage_capacity'] ?? 0); ?> Luggage Bag | <?php echo htmlspecialchars($v['ac_type'] ?? 'AC'); ?>
                            </div>
                            <?php if (!empty($v['cab_class_name'])): ?>
                            <span class="m-cab-class-chip"><?php echo htmlspecialchars($v['cab_class_name']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="m-cab-result-amenities-list">
                        <?php if ($s_type === 'hourly' && !empty($v['km_charges_text'])): ?>
                        <div class="m-cab-amenity-row">
                            <span class="material-symbols-outlined m-cab-amenity-icon" aria-hidden="true">speed</span>
                            <div class="m-cab-amenity-content">
                                <span class="m-cab-amenity-label">Km Charges</span>
                                <span class="m-cab-amenity-value"><?php echo htmlspecialchars($v['km_charges_text']); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="m-cab-amenity-row">
                            <span class="material-symbols-outlined m-cab-amenity-icon" aria-hidden="true">local_gas_station</span>
                            <div class="m-cab-amenity-content">
                                <span class="m-cab-amenity-label">Fuel Type</span>
                                <span class="m-cab-amenity-value"><?php echo htmlspecialchars(($v['fuel_type'] ?? '') !== '' ? $v['fuel_type'] : 'Petrol, Diesel, CNG'); ?></span>
                            </div>
                        </div>

                        <div class="m-cab-amenity-row">
                            <span class="material-symbols-outlined m-cab-amenity-icon m-cab-amenity-icon--green" aria-hidden="true">check_circle</span>
                            <div class="m-cab-amenity-content">
                                <span class="m-cab-amenity-label">Cancellation</span>
                                <span class="m-cab-amenity-value m-cab-amenity-value--green"><?php echo htmlspecialchars($v['cancellation_policy'] ?? 'Free before 6 hours from journey time.'); ?></span>
                            </div>
                        </div>

                        <div class="m-cab-amenity-row">
                            <span class="material-symbols-outlined m-cab-amenity-icon" aria-hidden="true">account_balance_wallet</span>
                            <div class="m-cab-amenity-content">
                                <span class="m-cab-amenity-label">Part Payment</span>
                                <span class="m-cab-amenity-value"><?php echo htmlspecialchars($v['part_payment'] ?? 'Pay 25% now and rest to driver'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="m-cab-result-footer">
                        <div class="m-cab-result-price">
                            <?php if ($display_rate): ?>
                            <span class="price-final">&#8377;<?php echo number_format((float)$display_rate); ?></span>
                            <?php else: ?>
                            <span class="price-final"><span class="price-unit">N/A</span></span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="m-cab-select-btn vehicle-action-btn" data-action="select-vehicle"
                            data-id="<?php echo (int)$v['id']; ?>"
                            data-name="<?php echo htmlspecialchars($v['name'] ?? '', ENT_QUOTES); ?>"
                            data-price="<?php echo $display_rate ?? 0; ?>"
                            data-image="<?php echo htmlspecialchars($v_img, ENT_QUOTES); ?>"
                            data-km="<?php echo htmlspecialchars($v['km_charges_text'] ?? '', ENT_QUOTES); ?>"
                            data-seats="<?php echo (int)($v['pax_capacity'] ?? 0); ?>"
                            data-bags="<?php echo (int)($v['luggage_capacity'] ?? 0); ?>"
                            data-ac="<?php echo htmlspecialchars($v['ac_type'] ?? '', ENT_QUOTES); ?>"
                            data-fuel="<?php echo htmlspecialchars(($v['fuel_type'] ?? '') !== '' ? $v['fuel_type'] : 'Petrol, Diesel, CNG', ENT_QUOTES); ?>">
                            <?php echo $is_search ? 'SELECT' : 'ENQUIRY'; ?>
                        </button>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <?php if ($footer_duration_text !== ''): ?>
        <p class="m-cab-footer-note">
            <span class="material-symbols-outlined" aria-hidden="true">directions_car</span>
            <?php echo htmlspecialchars($footer_duration_text); ?>
        </p>
        <?php endif; ?>

        <div class="m-cab-bottom-spacer" aria-hidden="true"></div>
    </main>

    <!-- Fixed price bar (shows after SELECT) -->
    <div id="mCabPriceBar" class="m-cab-price-bar is-hidden" role="region" aria-label="Selected cab summary" hidden>
        <div class="m-cab-price-bar-info">
            <div id="mCabBarVehicle" class="m-cab-price-bar-vehicle"></div>
            <div id="mCabBarTotal" class="m-cab-price-bar-total">—</div>
        </div>
        <button type="button" id="mCabBarContinue" class="m-cab-price-bar-cta" data-action="open-review">
            CONTINUE
        </button>
    </div>

    <!-- Review Details (bottom sheet) -->
    <div id="mCabReviewModal" class="m-cab-review-modal is-hidden" role="dialog" aria-modal="true"
        aria-labelledby="mCabReviewTitle" aria-hidden="true" hidden>
        <div class="m-cab-review-overlay" data-action="close-review"></div>
        <div class="m-cab-review-sheet">
            <div class="m-cab-review-header">
                <h2 id="mCabReviewTitle">Review Details</h2>
                <button type="button" class="m-cab-review-close" data-action="close-review" aria-label="Close review details">
                    <span class="material-symbols-outlined" aria-hidden="true">close</span>
                </button>
            </div>
            <div class="m-cab-review-body">
                <div class="m-cab-review-vehicle">
                    <div id="mCabReviewVehicle" class="m-cab-review-vname"></div>
                    <div id="mCabReviewService" class="m-cab-review-service"></div>
                </div>
                
                <div class="m-cab-review-route-strip">
                    <div class="m-cab-review-route-box">
                        <div class="m-cab-review-route-label" id="mCabReviewLabelPickup">Pick-up</div>
                        <div class="m-cab-review-route-val" id="mCabReviewPickup">N/A</div>
                    </div>
                    <div class="m-cab-review-route-badge" aria-hidden="true">
                        <span class="material-symbols-outlined">directions_car</span>
                    </div>
                    <div class="m-cab-review-route-box">
                        <div class="m-cab-review-route-label" id="mCabReviewLabelDrop">Drop-off</div>
                        <div class="m-cab-review-route-val" id="mCabReviewDrop">N/A</div>
                    </div>
                </div>

                <div class="m-cab-review-route-strip m-cab-review-date-strip">
                    <div class="m-cab-review-route-box">
                        <div class="m-cab-review-route-label">DATE</div>
                        <div class="m-cab-review-route-val" id="mCabReviewDate">N/A</div>
                    </div>
                    <div class="m-cab-review-route-box">
                        <div class="m-cab-review-route-label">TIME</div>
                        <div class="m-cab-review-route-val" id="mCabReviewTime">N/A</div>
                    </div>
                </div>

                <div class="m-cab-review-meta" id="mCabReviewMeta">
                    <div class="m-cab-review-meta-item" id="mCabReviewMetaSeats" hidden>
                        <span class="material-symbols-outlined" aria-hidden="true">group</span>
                        <span class="m-cab-review-meta-val" data-meta-val></span>
                    </div>
                    <div class="m-cab-review-meta-item" id="mCabReviewMetaBags" hidden>
                        <span class="material-symbols-outlined" aria-hidden="true">luggage</span>
                        <span class="m-cab-review-meta-val" data-meta-val></span>
                    </div>
                    <div class="m-cab-review-meta-item" id="mCabReviewMetaAc" hidden>
                        <span class="material-symbols-outlined" aria-hidden="true">ac_unit</span>
                        <span class="m-cab-review-meta-val" data-meta-val></span>
                    </div>
                    <div class="m-cab-review-meta-item" id="mCabReviewMetaFuel" hidden>
                        <span class="material-symbols-outlined" aria-hidden="true">local_gas_station</span>
                        <span class="m-cab-review-meta-val" data-meta-val></span>
                    </div>
                </div>

                <div class="m-cab-review-km" id="mCabReviewKm" hidden>
                    <span class="material-symbols-outlined" aria-hidden="true">speed</span>
                    <div class="m-cab-review-km-body">
                        <span class="m-cab-review-km-label">KM CHARGES</span>
                        <span class="m-cab-review-km-val" data-meta-val></span>
                    </div>
                </div>
                
                <div class="m-cab-review-divider"></div>

                <div class="m-cab-review-total-row">
                    <span>TOTAL ESTIMATED :</span>
                    <span id="mCabReviewTotal">—</span>
                </div>
                
                <fieldset class="m-cab-service-type" id="mCabReviewServiceGroup" hidden>
                    <legend>Select Service Type *</legend>
                    <label class="m-cab-radio" for="mReviewSvcDisposal">
                        <input type="radio" id="mReviewSvcDisposal" name="review_service_type" value="Disposal" data-action="set-service-type-review">
                        <span>Disposal (full day)</span>
                    </label>
                    <label class="m-cab-radio" for="mReviewSvcP2P">
                        <input type="radio" id="mReviewSvcP2P" name="review_service_type" value="Point to Point" data-action="set-service-type-review">
                        <span>Point to Point</span>
                    </label>
                </fieldset>
                
                <div id="mCabReviewMsg" class="m-cab-form-msg" role="status" aria-live="polite"></div>
                
                <div class="m-cab-review-footer">
                    <button type="button" id="mCabReviewSubmit" class="m-cab-review-submit" data-action="open-enquiry">
                        SUBMIT
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enquiry Summary (full-screen) -->
    <div id="m-cab-checkout-modal" class="m-cab-checkout-modal" role="dialog" aria-modal="true" aria-labelledby="mCabCheckoutTitle" aria-hidden="true" hidden>
        <div class="m-cab-checkout-panel">
            <div class="m-cab-checkout-header">
                <button type="button" class="m-cab-checkout-close" data-action="close-checkout" aria-label="Back to review details">
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                </button>
                <h2 id="mCabCheckoutTitle">Enquiry Summary</h2>
            </div>

            <div class="m-cab-checkout-body">
                <div class="m-cab-checkout-card" aria-live="polite">
                    <div class="m-cab-checkout-vehicle">
                        <div class="m-cab-checkout-vehicle-info">
                            <div id="m-modalSummaryVehicle" class="m-cab-checkout-vname"></div>
                            <div id="m-modalSummaryService" class="m-cab-checkout-service"></div>
                        </div>
                        <div class="m-cab-checkout-img-wrap">
                            <img id="m-modalVehicleImg" class="m-cab-checkout-img" alt="" hidden>
                        </div>
                    </div>

                    <div class="m-cab-checkout-divider" aria-hidden="true"></div>

                    <div class="m-cab-checkout-route">
                        <div class="m-cab-checkout-route-col">
                            <div class="m-cab-checkout-label" id="m-modalLabelPickup">Pick-up</div>
                            <div class="m-cab-checkout-val" id="m-modalPickup">N/A</div>
                        </div>
                        <div class="m-cab-checkout-route-col right">
                            <div class="m-cab-checkout-label" id="m-modalLabelDrop">Drop-off</div>
                            <div class="m-cab-checkout-val" id="m-modalDrop">N/A</div>
                        </div>
                    </div>

                    <div class="m-cab-checkout-datetime">
                        <div class="m-cab-checkout-route-col">
                            <div class="m-cab-checkout-label">Date</div>
                            <div class="m-cab-checkout-val" id="m-modalDate">N/A</div>
                        </div>
                        <div class="m-cab-checkout-route-col right">
                            <div class="m-cab-checkout-label">Time</div>
                            <div class="m-cab-checkout-val" id="m-modalTime">N/A</div>
                        </div>
                    </div>

                    <div class="m-cab-checkout-meta" id="m-modalMetaStrip">
                        <div class="m-cab-checkout-meta-item" id="m-modalMetaSeats" hidden>
                            <span class="material-symbols-outlined" aria-hidden="true">group</span>
                            <span class="m-cab-checkout-meta-val" data-meta-val></span>
                        </div>
                        <div class="m-cab-checkout-meta-item" id="m-modalMetaBags" hidden>
                            <span class="material-symbols-outlined" aria-hidden="true">luggage</span>
                            <span class="m-cab-checkout-meta-val" data-meta-val></span>
                        </div>
                        <div class="m-cab-checkout-meta-item" id="m-modalMetaAc" hidden>
                            <span class="material-symbols-outlined" aria-hidden="true">ac_unit</span>
                            <span class="m-cab-checkout-meta-val" data-meta-val></span>
                        </div>
                        <div class="m-cab-checkout-meta-item" id="m-modalMetaFuel" hidden>
                            <span class="material-symbols-outlined" aria-hidden="true">local_gas_station</span>
                            <span class="m-cab-checkout-meta-val" data-meta-val></span>
                        </div>
                        <div class="m-cab-checkout-meta-item" id="m-modalMetaKm" hidden>
                            <span class="material-symbols-outlined" aria-hidden="true">speed</span>
                            <span class="m-cab-checkout-meta-val" data-meta-val></span>
                        </div>
                    </div>
                </div>

                <div class="m-cab-checkout-card m-cab-checkout-card--price">
                    <p class="m-cab-checkout-total">
                        <span>Total Estimated</span>
                        <strong id="m-modalSummaryTotal">—</strong>
                    </p>
                </div>

                <form id="m-cab-checkoutForm" class="m-cab-checkout-form">
                    <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="vehicle_id" id="m-formVehicleId">
                    <input type="hidden" name="vehicle_name" id="m-formVehicleName">
                    <input type="hidden" name="service_type" id="m-formServiceType">
                    <input type="hidden" name="final_price" id="m-formFinalPrice">
                    <input type="hidden" name="pickup_location" id="m-formPickup">
                    <input type="hidden" name="drop_location" id="m-formDrop">
                    <input type="hidden" name="travel_date" id="m-formDate">
                    <input type="hidden" name="travel_time" id="m-formTime">
                    <input type="hidden" name="trip_type" id="m-formTripType">
                    <input type="hidden" name="duration" id="m-formDuration">
                    <input type="hidden" name="search_itinerary_details" id="m-formSearchItinerary">

                    <div class="m-cab-checkout-form-section">
                        <h3 class="m-cab-form-section-title">Guest Information</h3>
                        <div class="m-cab-field">
                            <label for="mGuestFname">First Name *</label>
                            <input type="text" id="mGuestFname" name="guest_name" autocomplete="given-name" required>
                        </div>
                        <div class="m-cab-field">
                            <label for="mGuestLname">Last Name *</label>
                            <input type="text" id="mGuestLname" name="guest_last_name" autocomplete="family-name" required>
                        </div>
                        <div class="m-cab-field">
                            <label for="mGuestSpecial">Special request (optional)</label>
                            <input type="text" id="mGuestSpecial" name="special_request" autocomplete="off">
                        </div>
                        <div class="m-cab-field">
                            <label for="mGuestPhone">Mobile number *</label>
                            <div class="m-cab-phone-row">
                                <span class="m-cab-phone-prefix" aria-hidden="true">+91</span>
                                <input type="tel" id="mGuestPhone" name="phone" class="m-cab-phone-input" inputmode="tel" autocomplete="tel" required>
                            </div>
                        </div>
                        <div class="m-cab-field">
                            <label for="mGuestEmail">Email *</label>
                            <input type="email" id="mGuestEmail" name="email" autocomplete="email" required>
                        </div>
                    </div>

                    <button type="submit" class="m-cab-submit-btn" id="m-btnSubmitModal">Submit Inquiry</button>
                    <div id="m-modalFormMsg" class="m-cab-form-msg" role="status" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/mobile_footer.php'; ?>
