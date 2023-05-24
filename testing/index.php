<style>
    /*! CSS Used from: http://localhost/star_m/assets/star/css/site-style.css */
    a {
        background-color: transparent;
    }

    a:active, a:hover {
        outline: 0;
    }

    @media print {
        *, *:before, *:after {
            background: transparent !important;
            color: #000 !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        a, a:visited {
            text-decoration: underline;
        }

        a[href]:after {
            content: " (" attr(href) ")";
        }

        a[href^="#"]:after {
            content: "";
        }
    }

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    *:before, *:after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    a {
        color: #337ab7;
        text-decoration: none;
    }

    a:hover, a:focus {
        color: #23527c;
        text-decoration: underline;
    }

    a:focus {
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px;
    }

    [class^="icon-"] {
        font-family: 'icomoon' !important;
        speak: none;
        font-style: normal;
        font-weight: 400;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .icon-office2:before {
        content: "\ed24";
    }

    a, a:focus, a:hover {
        outline: none;
        text-decoration: none;
        color: #727272;
    }

    a:hover, a:focus {
        color: #92c800;
    }

    div {
        margin: 0;
        padding: 0;
    }

    ::selection {
        color: #fff;
        background-color: #92c800;
    }

    ::-moz-selection {
        color: #fff;
        background-color: #92c800;
    }

    .top-bar-wrapper a:hover {
        color: #92c800;
    }

    .top-bar-wrapper a {
        color: inherit;
    }

    .submit-property {
        background: #92c800;
        padding: 8px 28px;
        -webkit-border-radius: 20px;
        -moz-border-radius: 20px;
        border-radius: 20px;
        color: #fff;
        border: solid 2px #92c800;
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        -ms-transition: all 0.3s;
        -o-transition: all 0.3s;
        transition: all 0.3s;
        font-size: 12px;
    }

    .submit-property i {
        margin-right: 10px;
        font-size: 12px !important;
    }

    .submit-property:hover {
        background-color: transparent;
        color: #92c800;
    }

    /*! CSS Used from: http://localhost/star_m/assets/star/css/dev.css */

    /*! CSS Used fontfaces */
    @font-face {
        font-family: 'icomoon';
        src: url(http://localhost/star_m/assets/star/fonts/icomoon.eot?gv32os);
        src: url('http://localhost/star_m/assets/star/fonts/icomoon.eot?gv32os#iefix') format('embedded-opentype'), url(http://localhost/star_m/assets/star/fonts/icomoon.ttf?gv32os) format('truetype'), url(http://localhost/star_m/assets/star/fonts/icomoon.woff?gv32os) format('woff'), url('http://localhost/star_m/assets/star/fonts/icomoon.svg?gv32os#icomoon') format('svg');
        font-weight: 400;
        font-style: normal;
    }
</style>

<div class="textwidget custom-html-widget" style="width: 180px">
    <div class="submit-property">
        <a href="#" data-toggle="modal" data-target="#ere_signin_modal" title="Submit Property"><i class="icon-office2"></i> Submit Property</a>
    </div>
</div>
<?php
