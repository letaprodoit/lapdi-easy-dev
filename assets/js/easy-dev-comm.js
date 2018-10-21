/**
 * Let A Pro Do IT! Easy Dev Communication
 *
 * @package		tspedev
 * @filename	easy-dev-comm.js
 * @version		1.0.0
 * @author		Sharron Denice, Let A Pro Do IT! (www.letaprodoit.com)
 * @copyright	Copyright 2018 SLet A Pro Do IT! (www.letaprodoit.com). All rights reserved
 * @license		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 *
 */
function getNativeWindow() {
    return window;
}

getNativeWindow().addEventListener('message', function (event) {
    if (event.origin !== 'null') {
        if ((event.origin !== this.window.location.origin)) {
            var obj = JSON.parse(event.data);

            if (event.origin == 'https://specials.sprouts.com' && obj.type && obj.type == 'analytics')
            {
                if ( obj.event_type && obj.event_type == 'page_view' || obj.event_type && obj.event_type == 'item_view' )
                {
                    if (TSPED_DEBUG)
                        console.log('Flipp View Change Notification: ', obj);

                    sfm_set_default_store(obj);
                }
                else if ( obj.event_type && obj.event_type == 'category_select' )
                {
                    if (TSPED_DEBUG)
                        console.log('Flipp Category Change Notification: ', obj);
                }
                else if ( obj.event_type && obj.event_type == 'flyer_select' )
                {
                    if (TSPED_DEBUG)
                        console.log('Flipp Flyer Change Notification: ', obj);
                }
            }
        }
    }
});