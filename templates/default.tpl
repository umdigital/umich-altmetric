<div class="umich-altmetric uma-display-<?=UmichAltmetric::$displayCounter;?> uma-donut-size<?=($atts['donut-size'] ? '-'. $atts['donut-size'] : null);?>">
    <ul class="items">
        <? foreach( $researchItems['items'] as $item ): ?>
        <li class="clearfix">
            <div class="altmetric-embed" data-badge-type="<?=($atts['donut-size'] ? $atts['donut-size'].'-' : null);?>donut" data-doi="<?=@$item->attributes->identifiers->dois[0];?>" data-isbn="<?=@$item->attributes->identifiers->isbns[0];?>" data-pmid="<?=@$item->attributes->identifiers->{'pubmed-ids'}[0];?>"></div>
            <h4><a href="https://www.altmetric.com/details.php?domain=<?=$_SERVER['HTTP_HOST'];?>&citation_id=<?=$item->id;?>"><?=$item->attributes->title;?></a></h4>
            <ul class="meta">
                <? if( $item->attributes->{'output-type'} == 'book' ): ?>
                <li>Book</li>
                <? elseif( @$researchItems['relationships']['journal'][ $item->relationships->journal->id ]->title ): ?>
                <li>Article in <em><?=$researchItems['relationships']['journal'][ $item->relationships->journal->id ]->title;?></em></li>
                <? endif; ?>
                <li><?=date( 'F Y', strtotime( $item->attributes->{'publication-date'} ) );?></li>
                <li><em><?=$item->attributes->{'historical-mentions'}->{'1w'};?></em> mentions in the past week</li>
            </ul>
        </li>
        <? endforeach; ?>
    </ul>
</div>
