<div class="welcome-panel-content">
    <h3>Percent complete</h3>

    <p class="about-description">Complete the actions.</p>

    <div class="welcome-panel-column-container">
        <div class="welcome-panel-column">
            <h4>Progress</h4>

            <input class="progress" type="text" value="<?php printf( "%4.0f", ( $progress * 100 ) ); ?>">
        </div>
        <div class="welcome-panel-column welcome-panel-last">
            <h4>Actions</h4>
            <ul id="sm_todo_items">
                <?php
                foreach ( $items as $item ) {
                    /* @var $item ITodoItem */
                    $color = 'red';
                    $icon = 'no';
                    if ( $item->isComplete() ) {
                        $color = 'green';
                        $icon = 'yes';
                    }
                    ?>
                    <li class="welcome-icon">
                        <div class="dashicons dashicons-<?php echo $icon; ?>"></div>
                        <a href="<?php echo $item->getLink(); ?>" class="" style="color: <?php echo $color; ?>">
                            <?php echo $item->getDescription(); ?>
                        </a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(function () {
            jQuery(".progress").css({
                'border': 0,
                'box-shadow': 'none'
            }).knob({
                'angleArc': 250,
                'angleOffset': -125,
                'fgColor': '#66CC66',
                'readOnly': true,
                'width': 240
            });
        });
    </script>
    <!-- column-container -->

</div>
