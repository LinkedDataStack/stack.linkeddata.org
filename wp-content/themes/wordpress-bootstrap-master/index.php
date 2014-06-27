<style type="text/css"><!--
		.map_image { display: block; width: 400px; height: 400px; position: relative; background-position: 0 0; background-repeat: no-repeat; }
		.map_image .map_link { display: block; position: absolute; text-indent: -999em; overflow: hidden; }
		.map_image #map_link_0 { width: 79px; height: 80px; top: 3px; left: 159px; }
		.map_image #map_link_1 { width: 81px; height: 76px; top: 51px; left: 269px; }
		.map_image #map_link_2 { width: 84px; height: 76px; top: 160px; left: 306px; }
		.map_image #map_link_3 { width: 84px; height: 76px; top: 271px; left: 264px; }
		.map_image #map_link_4 { width: 71px; height: 74px; top: 317px; left: 163px; }
		.map_image #map_link_5 { width: 74px; height: 74px; top: 268px; left: 54px; }
		.map_image #map_link_6 { width: 76px; height: 82px; top: 153px; left: 10px; }
	.map_image #map_link_7 { width: 72px; height: 73px; top: 55px; left: 56px; }
--></style>
		
<?php get_header(); ?>
			
<div class="clearfix row-fluid">
	<div class="span6">
		<div class="map_image" style="background-image: url('images/lifecyle.png');">
			<a class="map_link" id="interlink" title="interlinking" href="#"></a>
			<a class="map_link" id="enrichment" title="enrichment" href="#"></a>
			<a class="map_link" id="qa" title="quality analysis" href="#"></a>
			<a class="map_link" id="repair" title="repair" href="#"></a>
			<a class="map_link" id="search" title="search" href="#"></a>
			<a class="map_link" id="extract" title="extract" href="#"></a>
			<a class="map_link" id="store" title="store" href="#"></a>
			<a class="map_link" id="authoring" title="authoring" href="#"></a>
		</div>
	</div>
	<div class="span6">
		<div id="myCarousel" class="carousel slide">
		  <ol class="carousel-indicators">
		    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		    <li data-target="#myCarousel" data-slide-to="1"></li>
		    <li data-target="#myCarousel" data-slide-to="2"></li>
		  </ol>
		  <!-- Carousel items -->
		  <div class="carousel-inner">
		    <div class="active item"> <?php echo gcb(4);?> </div>
		    <div class="item"> <?php echo gcb(5);?></div>
		    <div class="item"> <?php echo gcb(6);?> </div>
		    <div class="item"> <?php echo gcb(7);?> </div>
		    <div class="item"> <?php echo gcb(8);?> </div>
		    <div class="item"> <?php echo gcb(9);?> </div>
		    <div class="item"> <?php echo gcb(10);?> </div>
				<div class="item"> <?php echo gcb(11);?> </div>
				<div class="item"> <?php echo gcb(12);?> </div>
		  </div>
		  <!-- Carousel nav -->
		  <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		  <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>

	</div>
</div>

<div id="content" class="clearfix row-fluid">

	<div id="main" class="span9 clearfix" role="main">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<?php get_template_part( 'content', get_post_format() ); ?>					
		<?php endwhile; ?>	
		
		<?php if (function_exists('page_navi')) { // if expirimental feature is active ?>
			
			<?php page_navi(); // use the page navi function ?>
			
		<?php } else { // if it is disabled, display regular wp prev & next links ?>
			<nav class="wp-prev-next">
				<ul class="clearfix">
					<li class="prev-link"><?php next_posts_link(_e('&laquo; Older Entries', "bonestheme")) ?></li>
					<li class="next-link"><?php previous_posts_link(_e('Newer Entries &raquo;', "bonestheme")) ?></li>
				</ul>
			</nav>
		<?php } ?>		
		
		<?php else : ?>
		
		<article id="post-not-found">
		    <header>
		    	<h1><?php _e("Not Found", "bonestheme"); ?></h1>
		    </header>
		    <section class="post_content">
		    	<p><?php _e("Sorry, but the requested resource was not found on this site.", "bonestheme"); ?></p>
		    </section>
		    <footer>
		    </footer>
		</article>
		
		<?php endif; ?>

	</div> <!-- end #main -->

	<?php get_sidebar(); // sidebar 1 ?>

</div> <!-- end #content -->

<?php get_footer(); ?>