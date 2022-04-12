<div class="<?=$instance['className'];?>">
    <ul class="items">
        <?php foreach( $altmetricRes['items'] as $item ): ?>
        <li class="clearfix">
            <?php if( $instance['badgeType'] ): ?>
            <div class="altmetric-embed" data-condense="true" data-badge-popover="<?=$instance['badgePosition'];?>" data-badge-type="<?=$instance['badgeType'];?>" data-doi="<?=@$item->attributes->identifiers->dois[0];?>" data-isbn="<?=@$item->attributes->identifiers->isbns[0];?>" data-pmid="<?=@$item->attributes->identifiers->{'pubmed-ids'}[0];?>"></div>
            <?php endif; ?>
            <h4><a href="https://www.altmetric.com/details.php?domain=<?=$_SERVER['HTTP_HOST'];?>&citation_id=<?=$item->id;?>"><?=$item->attributes->title;?></a></h4>
            <dl class="meta">
                <dt class="screen-reader-text">Publication Type</dt>
                <dd>
                    <?php if( $item->attributes->{'output-type'} == 'book' ): ?>
                    Book
                    <?php elseif( @$altmetricRes['relationships']['journal'][ $item->relationships->journal->id ]->title ): ?>
                    Article in <em><?=$altmetricRes['relationships']['journal'][ $item->relationships->journal->id ]->title;?></em>
                    <?php endif; ?>
                </dd>
                <dt class="screen-reader-text">Publication Date</dt>
                <dd><?=date( 'F Y', strtotime( $item->attributes->{'publication-date'} ) );?></dd>
                <dt class="screen-reader-text">Publication Mentions</dt>
                <dd><em><?=$item->attributes->{'historical-mentions'}->{'1w'};?></em> mentions in the past week</dd>
            </dl>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
