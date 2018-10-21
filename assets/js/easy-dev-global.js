/**
 * Sprout's Farmer's Market Global functions
 *
 * @package		tspedev
 * @filename	easy-dev-global.js
 * @version		1.0.0
 * @author		Sharron Denice, Let A Pro Do IT! (www.letaprodoit.com)
 * @copyright	Copyright 2018 SLet A Pro Do IT! (www.letaprodoit.com). All rights reserved
 * @license		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 *
 */
var TSPED_DEBUG = false;

function tspedev_set_default_store( data )
{
    var _cms_url = window.location.protocol + '//' + window.location.host; // default to prod URL;

    // cms_url is globally set
    if (typeof cms_url !== 'undefined' && typeof cms_url !== null) {
        _cms_url = cms_url;
    }
    // if data is just the store number properly store it
    if ( !isNaN(data) )
    {
        data = { "store_id": data };
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                var unataComm = new UnataComm();

                payload = {
                    'authenticated': true,
                    'ext_id': response.data.store_id,
                    'name': response.data.title,
                    'href': response.data.permalink,
                    'store': {
                        'authenticated': true,
                        'ext_id': response.data.store_id,
                        'name': response.data.title,
                        'external_url': response.data.permalink,
                    }
                };

                if (TSPED_DEBUG)
                    console.log('Flipp Store Changing to '+ response.data.store_id + ': ', response);

                unataComm.send({eventType: 'unata-update-store', payload: payload}, false, null);

                tspedev_set_cookie(response.data.cookie_name, response.data, 1);
            }
        }
    }

    xhttp.open('PUT', _cms_url + '/wp-json/spr-wp-rest/v1/store', true);
    xhttp.setRequestHeader('Content-type','application/json; charset=utf-8');
    xhttp.send( JSON.stringify(data) );
}

// Store the field that contains the image URL
function tspedev_save_image_url(image_html, field_id)
{
    var image_url = '';

    var regex = /src=\"(.*?)\" /;
    var found = image_html.match(regex);

    if (found)
        image_url = found[1];

    selected_image = "<img src='" + image_url + "' />";

    url_display = image_url;

    if (image_url == '')
        url_display = 'No image selected';

    image_id = '#' + field_id;

    prefix = jQuery(image_id + "_prefix").val();

    url_display_id = '#' + prefix + '_url_display';
    selected_image_id = '#' + prefix + '_selected_image';

    jQuery(image_id).val(image_url);
    jQuery(url_display_id).html(url_display);
    jQuery(selected_image_id).html(selected_image);
}//end tspedev_save_image_url

// Store the field that contains the image URL
function tspedev_show_media_window()
{
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;height=500&amp;width=640');
}//end tspedev_show_media_window

// Remove the field name from the #image_field
function tspedev_remove_image_url(field_id, message)
{
    image_id = '#' + field_id;

    prefix = jQuery(image_id + "_prefix").val();

    url_display_id = '#' + prefix + '_url_display';
    selected_image_id = '#' + prefix + '_selected_image';

    jQuery(image_id).val('');
    jQuery(url_display_id).html(message);
    jQuery(selected_image_id).html('');
}//end tspedev_remove_image_url

function tspedev_set_cookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));

    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + JSON.stringify(cvalue) + ";" + expires + ";path=/";
}

function tspedev_get_cookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');

    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return JSON.parse(c.substring(name.length, c.length));
        }
    }
    return "";
}