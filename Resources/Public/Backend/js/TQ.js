/***************************************************************
*  Copyright notice
*
 *  (c) 2011 Nico Korthals, TEQneers GmbH & Co. KG <info@teqneers.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
Ext.ns('TQ');

TQ = {

    // ------------------------------------------------------------------------
    clearCache: function() {
        var url = TQ.getAjaxController('clearCache');

        Ext.Ajax.request({
            url: url
        });
    },

    // ------------------------------------------------------------------------

    windowCurrent: null,
    windowCloseCallback: null,

    /**
     * Open window
     *
     * @param   string      url                 URL
     * @param   callback    onCloseCallback     Callback for "on close"
     */
    windowOpen: function(url, onCloseCallback) {
        TQ.windowCurrent = new Ext.Window({
            width: '95%',
            height: Ext.getBody().getHeight() * 0.9,
            modal: true,
            html: '<iframe src="'+Ext.util.Format.htmlEncode(url)+'"></iframe>',
            listeners: {
                close: function() {
                    TQ.windowCurrent = null;
                    // Trigger callback
                    TQ.windowClose();
                }
            }
        });

        TQ.windowCurrent.show();
        TQ.windowCloseCallback = onCloseCallback;
    },

    /**
     * Close current opened window
     */
    windowClose: function() {
        // Close window
        if( TQ.windowCurrent ) {
            try {
                TQ.windowCurrent.close();
            } catch(e) {
                // DEBUG
            }
        }

        // Window callback
        if( TQ.windowCloseCallback ) {

            try {
                TQ.windowCloseCallback();
            } catch(e) {
                // DEBUG
            }
        }
        TQ.windowCloseCallback = null;
    },

    // ------------------------------------------------------------------------

    /**
     * Redirect current page to url
     *
     * @param   string  url
     */
    redirect: function(url) {
        if( url ) {
            window.location.href = url;
        }
    },

    // ------------------------------------------------------------------------

    /**
     * Get icon code
     *
     * All icons are defined in  TQ\TQBase\Backend\Controller\AbstractController
     *
     * @param   string  key
     * @return  string
     */
    icon: function(key) {
        var ret;

        var storeKey = key;

        if( TQ.Conf.Icons && TQ.Conf.Icons[key] ) {
            ret = TQ.Conf.Icons[key];
        }

        return ret;
    },

    /**
     * Translate token (with fallback to base)
     *
     * @param   string  key
     * @return  string
     */
    translate: function(key) {
        var ret;

        var storeKey = key;

        if( TQ.Conf.Lang && TQ.Conf.Lang[storeKey] ) {
            // Try local first
            ret = TQ.Conf.Lang[storeKey];
        } else if( TQ.Conf.Configuration && TQ.Conf.LangBase[storeKey] ) {
            // Fallback to base
            ret = TQ.Conf.LangBase[storeKey];
        } else {
            // Not translatable
            ret = '[['+key+']]';
        }

        return ret;
    },

    /**
     * Translate token (only local)
     *
     * @param   string  key
     * @return  string
     */
    translateLocal: function(key) {
        var ret;

        var storeKey = key;

        if( TQ.Conf.Lang && TQ.Conf.Lang[storeKey] ) {
            ret = TQ.Conf.Lang[storeKey];
        } else {
            ret = '[['+key+']]';
        }

        return ret;
    },

    /**
     * Translate token (only base)
     *
     * @param   string  key
     * @return  string
     */
    translateBase: function(key) {
        var ret;

        var storeKey = key;

        if( TQ.Conf.Configuration && TQ.Conf.LangBase[storeKey] ) {
            ret = TQ.Conf.LangBase[storeKey];
        } else {
            ret = '[['+key+']]';
        }

        return ret;
    },

    /**
     * Get configuration key
     *
     * @param   string  key
     * @param   mixed   defaultValue
     * @return  string
     */
    getConfiguration: function(key, defaultValue) {
        var ret = defaultValue;
        var storeKey = key;

        if( TQ.Conf.Configuration && TQ.Conf.Configuration[storeKey] ) {
            ret = TQ.Conf.Configuration[storeKey];
        }

        return ret;
    },

    /**
     * Set configuration
     *
     * @param   string  key
     * @param   mixed   value   Configuration value
     * @return  string
     */
    setConfiguration: function(key, value) {
        var storeKey = key;

        ret = TQ.Conf.Configuration[storeKey] = value;
    },

    /**
     * Get url (and replace some parts, these parts must be defined in php controller)
     *
     * @param   string  key
     * @param   object  conf
     * @return  string
     */
    getUrl: function(key, conf) {
        var url = TQ.getConfiguration('url.'+key);

        // Replace some parts
        if( !Ext.isEmpty(conf) && Ext.isObject(conf) ) {
            Ext.iterate(conf, function(key, value) {
                key = Ext.util.Format.uppercase(key);
                var regExp = new RegExp("T3_DATABASE_ENTITY_"+key,"g");

                // TODO: we should add urlencode here but Ext doesn't have an urlEncode
                //       (the urlEncode method doesn't fit here)
                url = url.replace(regExp, value);
            });
        }
        return url;
    },

    /**
     * Get and build ajax controller url
     *
     * @param  string  method
     * @return string
     */
    getAjaxController: function(method) {
        // TODO: we should add urlencode here but Ext doesn't have an urlEncode
        //       (maybe the urlEncode method doesn't fit here)
        return TQ.getConfiguration('ajaxController')+'&cmd='+method;
    },

    // ------------------------------------------------------------------------

    /**
     * Call action (defined in controller, used with JsonExpression)
     *
     * @param   string  action
     */
    callAction: function(action) {
        action = action; //.replace(/\./g, '_' );

        if( TQ.Conf.Actions && TQ.Conf.Actions[action] ) {
            return TQ.Conf.Actions[action]();
        }
    },
    // ------------------------------------------------------------------------

    /**
     * Confirm lock action
     *
     * @param   string      title       Entity identifiation (eg. title)
     * @param   function    callback    Callback when entity can be deleted
     */
    confirmLock: function(title, callback) {
        var bodyMsg = TQ.translateBase('message.confirm.lock.body');
        bodyMsg = bodyMsg.replace(/\{0\}/g, Ext.util.Format.htmlEncode(title) );

        var boxHeight = 150;

        var confirmItems = [{
            xtype: 'label',
            text: bodyMsg,
            style: 'display: block; padding: 10px;font-size: 14px;'
        }];

        var reasonLockList = TQ.getConfiguration("reasonLockList");

        if( !Ext.isEmpty(reasonLockList) ) {
            var storeData = [];

            for(var i in reasonLockList) {
                storeData.push([i, reasonLockList[i]]);
            }

            confirmItems.push({
                xtype: 'combo',
                id: 'reasonLock',
                fieldLabel: TQ.translate('label.lock.reason'),
                emptyText: TQ.translate('label.lock.reason'),
                forceSelection: false,
                editable: false,
                autoSelect: true,
                mode: 'local',
                triggerAction: 'all',
                store: new Ext.data.ArrayStore({
                    fields: [
                        'id',
                        'title'
                    ],
                    data: storeData
                }),
                valueField: 'id',
                displayField: 'title',
                allowBlank: false,
                width: 350
            });
        }

        var frmConfirm = new Ext.Window({
            xtype: 'form',
            width: 400,
            boxMinHeight: boxHeight,
            minHeight: boxHeight,
            height: boxHeight,
            modal: true,
            title: TQ.translateBase('message.confirm.lock.title'),
            items: confirmItems,
            buttons: [
                {
                    text: TQ.translateBase('message.confirm.lock.button.yes'),
                    handler: function(cmp, e) {
                        var lockReasonField = Ext.getCmp('reasonLock');

                        if( lockReasonField.validate() ) {
                            var lockReason = lockReasonField.getValue();
                            frmConfirm.destroy();
                            if( callback ) {
                                callback(lockReason);
                            }
                        }
                    }
                },{
                    text: TQ.translateBase('message.confirm.lock.button.cancel'),
                    handler: function(cmp, e) {
                        frmConfirm.destroy();
                    }
                }
            ]
        });
        frmConfirm.show();
    },

    /**
     * Confirm unlock action
     *
     * @param   string      title       Entity identifiation (eg. title)
     * @param   function    callback    Callback when entity can be deleted
     */
    confirmUnlock: function(title, callback) {
        var bodyMsg = TQ.translateBase('message.confirm.unlock.body');
            bodyMsg = bodyMsg.replace(/\{0\}/g, Ext.util.Format.htmlEncode(title) );

        var boxHeight = 150;

        var confirmItems = [{
            xtype: 'label',
            text: bodyMsg,
            style: 'display: block; padding: 10px;font-size: 14px;'
        }];

        var frmConfirm = new Ext.Window({
            xtype: 'form',
            width: 400,
            boxMinHeight: boxHeight,
            minHeight: boxHeight,
            height: boxHeight,
            modal: true,
            title: TQ.translateBase('message.confirm.unlock.title'),
            items: confirmItems,
            buttons: [
                {
                    text: TQ.translateBase('message.confirm.unlock.button.yes'),
                    handler: function(cmp, e) {
                        frmConfirm.destroy();
                        if( callback ) {
                            callback();
                        }
                    }
                },{
                    text: TQ.translateBase('message.confirm.unlock.button.cancel'),
                    handler: function(cmp, e) {
                        frmConfirm.destroy();
                    }
                }
            ]
        });
        frmConfirm.show();
    },

    /**
     * Confirm delete action
     *
     * @param   string      title       Entity identifiation (eg. title)
     * @param   function    callback    Callback when entity can be deleted
     */
    confirmDelete: function(title, callback) {
        var bodyMsg = TQ.translateBase('message.confirm.delete.body');
        bodyMsg = bodyMsg.replace(/\{0\}/g, Ext.util.Format.htmlEncode(title) );

        var boxHeight = 150;

        var confirmItems = [{
            xtype: 'label',
            text: bodyMsg,
            style: 'display: block; padding: 10px;font-size: 14px;'
        }];

        var frmConfirm = new Ext.Window({
            xtype: 'form',
            width: 400,
            boxMinHeight: boxHeight,
            minHeight: boxHeight,
            height: boxHeight,
            modal: true,
            title: TQ.translateBase('message.confirm.delete.title'),
            items: confirmItems,
            buttons: [
                {
                    text: TQ.translateBase('message.confirm.delete.button.yes'),
                    handler: function(cmp, e) {
                        frmConfirm.destroy();
                        if( callback ) {
                            callback();
                        }
                    }
                },{
                    text: TQ.translateBase('message.confirm.delete.button.cancel'),
                    handler: function(cmp, e) {
                        frmConfirm.destroy();
                    }
                }
            ]
        });
        frmConfirm.show();
    },

    // ------------------------------------------------------------------------

    /**
     * Highlight text in grid
     *
     * @copyright	Stefan Gehrig (TEQneers GmbH & Co. KG) <gehrig@teqneers.de>
     */
    highlightText: function(node, search, cls) {
        try {
            search		= search.toUpperCase();
            var skip	= 0;
            if (node.nodeType == 3) {
                var pos = node.data.toUpperCase().indexOf(search);
                if (pos >= 0) {
                    var spannode		= document.createElement('span');
                    spannode.className	= cls || 'TQ-search-highlight';
                    var middlebit		= node.splitText(pos);
                    var endbit			= middlebit.splitText(search.length);
                    var middleclone		= middlebit.cloneNode(true);
                    spannode.appendChild(middleclone);
                    middlebit.parentNode.replaceChild(spannode, middlebit);
                    skip = 1;
                }
            } else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
                for (var i = 0; i < node.childNodes.length; ++i) {
                    i += TQ.highlightText(node.childNodes[i], search);
                }
            }
        } catch(e) {}
        return skip;
    },

    /**
     * Check if highlight text is available
     *
	 *  (c) 2011 Nico Korthals, TEQneers GmbH & Co. KG <info@teqneers.de>
     */
    highlightTextExists: function(value, search) {
        search		= search.toUpperCase();
        var skip	= 0;

        var pos = value.toUpperCase().indexOf(search);
        if (pos >= 0) {
            return true;
        }

        return false;
    }
}

cl = function() {
    if ( window.console && window.console.log ) {
        console.log.call(console,arguments);
    }
};
