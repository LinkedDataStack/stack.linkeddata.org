<style type="text/css"><!--
		.map_image { display: block; width: 400px; height: 400px; position: relative; background-position: 0 0; background-repeat: no-repeat; }
		.map_image .map_link { display: block; position: absolute; text-indent: -999em; overflow: hidden; }
--></style>
		
<?php get_header(); ?>
			
<div class="clearfix row-fluid">
	<div class="span6">
		<div class="map_image" style="background-image: url('http://stack.linkeddata.org/wp-content/uploads/2013/08/lifecyle.png');">
			<a class="map_link" id="interlink" title="interlinking" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="enrichment" title="enrichment" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="qa" title="quality analysis" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="repair" title="repair" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="search" title="search" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="extract" title="extract" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="store" title="store" href="#" onclick="alert(this.id)"></a>
			<a class="map_link" id="authoring" title="authoring" href="#" onclick="alert(this.id)"></a>
		</div>
	</div>
	<div class="span6">
		<?php // Get RSS Feed(s)
		include_once( ABSPATH . WPINC . 'do/rss.php' );
		// change the default feed cache recreation period to 2 hours
		function return_0(){
		    return (int) 0;
		}
		// adds the filter to set cache lifetime
		add_filter( 'wp_feed_cache_transient_lifetime' , 'return_0' );
		// Get a SimplePie feed object from the specified feed source.
		$rss_cat = fetch_feed( 'http://stack.linkeddata.org/download/rss.php?&categories-only' );
		if ( ! is_wp_error( $rss_cat ) ) : // Checks that the object is created correctly
		    $categories = $rss_cat->get_categories();
		endif;
		?>
		<h2>The Stack</h4>
		<p> The Linked Data Stack comprises a number of tools for managing the life-cycle of Linked Data. The life-cycle
		comprises in particular the stages:</p>
		<ul>
			<?php foreach ( $categories as $cat ) : 
		    $categories_terms[]=$cat->get_term(); 
				?>
    		<li> <?php echo $cat->get_term(); ?> </li>
    	<?php endforeach; ?>
		</ul>

		<div id="myCarousel" class="carousel slide">
		  <!-- Carousel items -->
		  <div class="carousel-inner">
				<?php
				$rss = fetch_feed( 'http://stack.linkeddata.org/download/rss.php' );
				if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly
					$rss_items = $rss->get_items();
					$first = TRUE;
    			foreach ( $rss_items as $item ) :
    				unset($item_categories); 
    				foreach ($item->get_categories() as $category){
    					if(in_array($category->get_term(), $categories_terms))
    						$item_categories[] = $category->get_term();
    				}
    				if(isset($item_categories)):
							?>
							<div class="item <?php if($first){ echo ' active'; $first=FALSE; } ?>"> 
								<div class="component">
									<h4>Categories: <?php echo implode(" ", $item_categories); ?></h4>
					    		<ul class="media-list">
										<li class="media"> 
											<?php if ($enclosure = $item->get_enclosure()){
												echo '<a class="pull-left" href="'.$item->get_link().'" target="_blanc"><img class="media-object" src="'.$enclosure->get_link().'" /></a>';
											} 
											$text = $item->get_description();
											if(strlen($item->get_description())>400){
												$pos=strpos($item->get_description(), ' ', 400);
												$text = substr($item->get_description(),0,$pos)."...";
											}
											?>
			    						<div class="media-body">
			    							<strong><?php echo $item->get_title(); ?></strong>: <?php echo $text ; ?><br>
												<a class="btn btn-mini btn-link" href="<?php echo $item->get_permalink(); ?>" target="new"> Learn more </a>
			    						</div>
			  						</li>
			  					</ul>	
				    		</div> 
			    		</div>
			    		<?php
			 			endif;
		    	endforeach; 
				endif; 
				?>
		  </div>
		  <!-- Carousel nav -->
		  <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		  <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>

	</div>
</div>

<?php get_footer(); ?>
