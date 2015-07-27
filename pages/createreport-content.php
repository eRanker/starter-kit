<?php global $erapi_allfactors; ?>
<form class="seocheck_newreport" method="POST" action="">

    <div class="form-group">
        <label for="sc_url">URL:</label>
        <input type="text" class="form-control" id="sc_url" name="sc_url" size="65" value="<?php echo (isset($_POST['sc_url']) ? htmlspecialchars($_POST['sc_url']) : ''); ?>" placeholder="www.yourcompany.com">
    </div>
    
      <strong> Factors: </strong>                 
    <span id="seocheck_eranker_selectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer">
        <a href="javascript:jQuery('.seocheck_newreport input[name^=factorsGroup]').prop('checked', true);jQuery('.seocheck_newreport #seocheck_eranker_selectall').hide();jQuery('.seocheck_newreport #seocheck_eranker_deselectall').show();">
            Select All
        </a>
    </span>
    <span id="seocheck_eranker_deselectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer; display: none;">
        <a href="javascript:jQuery('.seocheck_newreport input[name^=factorsGroup]').prop('checked', false);jQuery('.seocheck_newreport #seocheck_eranker_selectall').show();jQuery('.seocheck_newreport #seocheck_eranker_deselectall').hide();">
            Deselect All
        </a>
    </span>
    <br />
    <br />    
    <br />
    <?PHP
    if (!empty($erapi_allfactors)) {
        foreach ($erapi_allfactors as $key => $value) {
            $is_checked = false;
            if (in_array($key, $erapi_accountinfo->plan->default_factors)) {
                $is_checked = TRUE;
            }
            ?>
            <label class="checkbox-inline" style="width: 15%; margin-left: 0px !important;">
                <input id="factor_<?PHP echo $key ?>" type="checkbox" name="factorsGroup[]" <?PHP echo $is_checked ? 'checked="checked"' : '' ?> value="<?PHP echo $key ?>"><?PHP echo $value->friendly_name ?>
            </label>
        <?PHP } ?> 
    <?PHP } ?>


<br />
<br />

    <button type="submit" class="btn btn-default">Create Report</button>

</form>
