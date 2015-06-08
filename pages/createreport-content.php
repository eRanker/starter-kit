
<form class="seocheck_newreport" method="POST" action="">
    <table class="form-table seocheck_table seocheck_noborder seocheck_nomargin">
        <tr class="row_even seocheck_bglgray">
            <td class="seocheck_nobg" colspan="2">
                <label title="Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com." for="sc_url">URL:</label><br/>
                <input title="Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com." type="text" placeholder="www.yourcompany.com" id="sc_url" name="sc_url" size="65" value="<?php echo (isset($_POST['sc_url']) ? htmlspecialchars($_POST['sc_url']) : ''); ?>" />
            </td>
        </tr>            
        <tr class="row_even seocheck_bglgray">
            <td class="seocheck_nobg" colspan="2">
                Factors                    
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
            </td>
        </tr>           
        <tr class="row_even seocheck_bglgray">
            <td class="seocheck_nobg" colspan="2">
                <?PHP
                if (!empty($erapi_allfactors)) {
                    foreach ($erapi_allfactors as $key => $value) {
                        $is_checked = false;
                        if (in_array($key, $erapi_accountinfo->plan->default_factors)) {
                            $is_checked = TRUE;
                        }
                        ?>
                        <label class="factor_list_checkbox" title="" for="factor_<?PHP echo $key ?>">
                            <input id="factor_<?PHP echo $key ?>" type="checkbox" name="factorsGroup[]" <?PHP echo $is_checked ? 'checked="checked"' : '' ?> value="<?PHP echo $key ?>"><?PHP echo $value->friendly_name ?>
                        </label>
                    <?PHP } ?> 
                <?PHP } ?>
            </td>
        </tr>

    </table>        
    <div class="seocheck_padded">
        <input type="submit" class="button-primary er_createreport_plugin" value="Create Report">
    </div>
</form>
