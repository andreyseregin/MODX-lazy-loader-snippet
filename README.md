# MODX-lazy-loader-snippet

To enable lazy loading of iframes and imgs import these libraries (jquery, intersection-observer pollyfill and lazyload js):
    
Import in HTML

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intersection-observer@0.5.1/intersection-observer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@11.0.5/dist/lazyload.min.js"></script>
    
ADD THIS TO JS CODE
// JS FOR INITIALISING LAZYLOAD JS
var lazyLoadInstance = new LazyLoad({
   elements_selector: ".lazy-load"
});

// JS code for lazy loading youtube iframes from img on hover
$('.youtube-lazy-load').mouseover(function() {
   if(!$(this).attr('data-src-video')){
      return;
   }
            
   const src = $(this).attr('data-src-video');
   const allow = $(this).attr('allow');
   const style = $(this).attr('style');
   $(this).parent().html('<iframe src="'+ src +'" allow="'+ allow +'" style="'+ style +'"></iframe>')
});
