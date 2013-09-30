WS-Flexslider

A simple WordPress plugin that uses the FlexSlider jQuery plugin. Slider displays 5 most recent posts from the "Featured" category.

Example of how to include in template file:


<?php if (function_exists('home_slider')) {
    home_slider();
}?>


And that's it....