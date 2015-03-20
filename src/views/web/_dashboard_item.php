<?php
/**
 * Default template for a single chunk in the accordion
 *
 * @var string $groupId
 * @var string $targetId
 * @var string $triggerContent
 * @var string $targetContent
 * @var bool   $opened
 */

$_opened = ( isset( $opened ) && true === $opened ) ? 'panel-body-open in' : null;
?>
<div class="panel panel-default panel-dsp">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a class="accordion-toggle"
                data-toggle="collapse"
                data-parent="#<?php echo $groupId; ?>"
                href="#<?php echo $targetId; ?>">
                <?php echo $triggerContent; ?>
            </a>
        </h4>
    </div>
    <div id="<?php echo $targetId; ?>" class="panel-collapse collapse <?php echo $_opened; ?>">
        <div class="panel-body">
            <?php echo $targetContent; ?>
        </div>
    </div>
</div>
