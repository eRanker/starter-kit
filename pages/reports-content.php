
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Report ID</th>
                <th>URL</th>
                <th class="hidden-md hidden-sm hidden-xs">Factors</th>
                <th style="text-align: center">Date Created</th>
                <th style="text-align: center">Status</th>
                <th style="text-align: center"></th>
            </tr>
        </thead>
        <tbody>
            <?PHP foreach ($report->items as $singleReport) { ?>
                <tr>
                    <td><?PHP echo $singleReport->id ?></td>
                    <td><a href="<?PHP echo $singleReport->url ?>" target="_blank"> <?PHP echo $singleReport->url ?></a></td>
                    <td class="hidden-md hidden-sm hidden-xs"><div style="overflow: hidden;   width:100%; max-width: 300px; text-overflow: ellipsis; white-space: nowrap"><?PHP echo implode(", ", $singleReport->factors) ?></div></td>
                    <td style="text-align: center"><?PHP echo $singleReport->date_created ?></td>
                    <td style="text-align: center"><span class="label <?PHP echo strcasecmp('DONE', $singleReport->status) === 0 ? 'label-success' : 'label-danger' ?>"><?PHP echo $singleReport->status ?></span></td>
                    <td style="text-align: center" style="text-align: center"><a href="/index.php?p=report&id=<?PHP echo $singleReport->id ?>" ><i class="fa fa-eye fa-fw"></i> View Report</a></td>
                </tr>
            <?PHP } ?>
        </tbody>
    </table>
</div>