<?php
/*

    To enable lazy loading of iframes and imgs use the following code:
    
    // HTML
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intersection-observer@0.5.1/intersection-observer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@11.0.5/dist/lazyload.min.js"></script>
    
    // JS
    var lazyLoadInstance = new LazyLoad({
        elements_selector: ".lazy-load"
    });

    If iframe is a youtube video then it will change iframe into img and then to load iframe I recommend the following code:
    
    // JS code for lazy loading iframes from img on hover
    $('.youtube-lazy-load').mouseover(function() {
        if(!$(this).attr('data-src-video')){
            return;
        }
            
        const src = $(this).attr('data-src-video');
        const allow = $(this).attr('allow');
        const style = $(this).attr('style');
        $(this).parent().html('<iframe src="'+ src +'" allow="'+ allow +'" style="'+ style +'"></iframe>')
    });
*/

$content = $input; //$input is the exact source to be attacked

// Initialisation
$dom = new DOMDocument();
$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
$xp = new DOMXPath($dom);
// END

// FIND IFRAMES AND IMGS
$iframes = $xp->query('//iframe');
$images = $xp->query('//img');
// END

// Start editing iframes

foreach ( $iframes as $iframe) {
    
 // W3C does not like set width, height, frameborder :(
 $width = $iframe->getAttribute('width');
 $height = $iframe->getAttribute('height');
 $border = $iframe->getAttribute('frameborder');
 if(!$border){
     $border = 'inherit';
 }
 if($height && $height !== '100%' && $height !== '0'){
     $height = $height.'px';
 } else {
     $height = '100%';
 }
 
 $iframe->setAttribute('style', 'width:100%;height:'.$height.';border:'.$border.';');
 $iframe->removeAttribute('width');
 $iframe->removeAttribute('height');
 $iframe->removeAttribute('frameborder');

 $existing_data_src = $iframe->getAttribute("data-bgfit");
 if(!$existing_data_src){
     $existing_src = $iframe->getAttribute("src");
     $iframe_allow = $iframe->getAttribute("allow");
     
     // Add NoScript tag -- NEEDS IMPROVEMENT
     $noscript = $dom->createElement("noscript");
     $noscript->appendChild($iframe->cloneNode());
     $iframe->parentNode->insertBefore($noscript, $iframe->nextSibling);
     // END
     
     // Check if youtube video
     if ($existing_src && strpos($existing_src, 'youtube') !== false) { // IF TRUE
        $iframe->removeAttribute("src");
        $iframe_id = substr($existing_src, strrpos($existing_src, '/') + 1); // GET VIDEO ID
        
        $iframe_img = $dom->createElement("img"); // CREATE IMG FOR PHP DOM
        $iframe_img->setAttribute("data-src", "https://img.youtube.com/vi/{$iframe_id}/sddefault.jpg"); // GET THUMBNAIL IMG FROM YOUTUBE
        $iframe_img->setAttribute("data-src-video", $existing_src); // SAVE CURRENT VIDEO URL
        $iframe_img->setAttribute("class", "lazy-load youtube-lazy-load {$iframe->getAttribute('class')}"); // TRIGGER LAZY LOADING
        $iframe_img->setAttribute("allow", $iframe_allow);
        // CHECK IF PARENT HAS A SET HEIGHT
        if(strpos($iframe->parentNode->getAttribute("style"), 'height') !== false){
            $iframe_img->setAttribute('style', 'width:100%;height:100%;border:'.$border.';');
        } else {
            $iframe_img->setAttribute('style', 'width:100%;height:150px;border:'.$border.';');
        }
        // END
        
        // SAVE IMG AND REMOVE IFRAME
        $iframe->parentNode->insertBefore($iframe_img, $iframe->nextSibling);
        $iframe->parentNode->removeChild($iframe);
        // END
     } else { // IF FALSE
        $iframe->removeAttribute("src");
        $iframe->setAttribute("data-src", $existing_src);
        $iframe->setAttribute("class", "lazy-load {$iframe->getAttribute('class')}");
     }
     // END 
 }

}

// Start editing images
foreach ( $images as $image) {

 $existing_data_src = $image->getAttribute("data-bgfit"); // Some plugins use img src that will prevent lazy loading for these imgs :)
 
 // Start check
 if(!$existing_data_src){
     $existing_src = $image->getAttribute("src");
     $noscript = $dom->createElement("noscript");
     $noscript->appendChild($image->cloneNode());
     $image->parentNode->insertBefore($noscript, $image->nextSibling);
     // change src to data-src
     if ($existing_src) {
        $image->removeAttribute("src");
        $image->setAttribute("data-src", $existing_src);
        $image->setAttribute("class", "lazy-load {$image->getAttribute('class')}");
     }
 }
 // End check

}
// End check


return $dom->saveHTML(); // Save data
