<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

/**
* @var $APPLICATION
* @var $USER
*/

if (!$USER->IsAdmin()) {
    LocalRedirect('/stream/');
}

$APPLICATION->SetTitle(title: 'Контроль старта рабочего дня'); ?>

    <div class="components_content">
        <div class="component">
            <?php $APPLICATION->IncludeComponent(
                componentName: 'dom.workdayblock:settings',
                componentTemplate: '.default'
            );?>
        </div>
    </div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>