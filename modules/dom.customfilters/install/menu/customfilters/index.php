<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle(title: 'Фильтры CRM'); ?>

    <div class="components_content">
        <div class="component">
            <?php $APPLICATION->IncludeComponent(
                componentName: 'dom.customfilters:settings',
                componentTemplate: '.default'
            );?>
        </div>
    </div>

<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>