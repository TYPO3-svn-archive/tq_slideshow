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

Ext.ns('TQ.Backend');

TQ.Backend.SlideshowGrid = Ext.extend(TQ.Backend.AbstractGrid, {

    constructor: function(config) {
        TQ.Backend.SlideshowGrid.superclass.constructor.call(this, config);

        this.title = TQ.translate('title.module.slideshow');


		console.log(TQ.Conf.addOn);

    },

    createAction: function(ob, cmd) {
        var me = this;

        TQ.windowOpen( TQ.getUrl('slideshow.create'), function() {
            me.reloadAction();
        });
    },

    createStore: function() {
        var store = new Ext.data.Store({
            storeId: 'TQSlideshowGrid',
            autoLoad: true,
            remoteSort: true,
            url: TQ.getAjaxController('getList'),
            reader: new Ext.data.JsonReader({
                    totalProperty: 'results',
                    root: 'rows'
                },[
                    {name: 'uid', type: 'int'},
                    {name: 'title', type: 'string'},
                    {name: 'hidden', type: 'int'},
                    {name: 'deleted', type: 'int'},
                    {name: 'locked', type: 'int'},
                    {name: 'type', type: 'string'},
					{name: '_endtimeDate', type: 'string'},

                    {name: '_category', type: 'string'},
                    {name: '_client', type: 'string'},
                    {name: '_clientLocked', type: 'int'},
                    {name: '_contact', type: 'string'},
                    {name: '_url_preview', type: 'string'},
                    {name: '__status__', type: 'string'}
                ]
            ),
            sortInfo: {
                field	 : 'uid',
                direction: 'DESC'
            },
            baseParams: {
                start               : 0,
                limit               : Ext.encode( TQ.getConfiguration('limitCount') ),
                sort                : TQ.getConfiguration('sortField', 'uid'),
                dir                 : TQ.getConfiguration('sortField', 'ASC'),
                criteriaFulltext    : Ext.encode( TQ.getConfiguration('criteriaFulltext') ),
                criteriaCategory    : Ext.encode( TQ.getConfiguration('criteriaCategory') ),
                criteriaType        : Ext.encode( TQ.getConfiguration('criteriaType') ),
                criteriaStatus      : Ext.encode( TQ.getConfiguration('criteriaStatus') ),
				criteriaEndtime    : Ext.encode( TQ.getConfiguration('criteriaEndtime') ),
            },
            listeners: {
                beforeload: function() {
                    this.baseParams.criteriaStatus          = Ext.encode( TQ.getConfiguration('criteriaStatus') );
                    this.baseParams.criteriaType            = Ext.encode( TQ.getConfiguration('criteriaType') );
                    this.baseParams.criteriaCategory        = Ext.encode( TQ.getConfiguration('criteriaCategory') );
                    this.baseParams.criteriaFulltext        = Ext.encode( TQ.getConfiguration('criteriaFulltext') );
					this.baseParams.criteriaEndtime         = Ext.encode( TQ.getConfiguration('criteriaEndtime') );
					this.removeAll();
                }
            }
        });

        return store;
    },

    createGrid: function() {
        var me = this;

        var toolbarShowClick = function(record) {
            var url = TQ.getUrl('slideshow.show', {uid: record.get("uid")});

            TQ.windowOpen( url, function() {
                me.reloadAction();
            });
        }

        var toolbarEditClick = function(record) {
            var url = TQ.getUrl('slideshow.edit', {uid: record.get("uid")});

            TQ.windowOpen( url, function() {
                me.reloadAction();
            });
        }

        var toolbarPreviewClick = function(record) {
            var url = record.get("_url_preview");
            var previewWin = window.open(url, 'TQ-Slideshow', "width=900,height=800,resizable=yes,location=yes,menubar=yes,scrollbars=yes,status=yes");
            previewWin.focus();
            return false;
        }

        var toolbarHideClick = function(record) {
            Ext.Ajax.request({
                url: TQ.getAjaxController('hide'),
                callback: function(options, success, response) {
                    if (response.responseText === '1') {
                        // reload the records and the table selector
                        me.reloadAction();
                    } else {
                        alert('ERROR: ' + response.responseText);
                    }
                },
                params: {
                    'uid': Ext.encode( record.get("uid") )
                }
            });
        }

        var toolbarUnhideClick = function(record) {
            Ext.Ajax.request({
                url: TQ.getAjaxController('unhide'),
                callback: function(options, success, response) {
                    if (response.responseText === '1') {
                        // reload the records and the table selector
                        me.reloadAction();
                    } else {
                        alert('ERROR: ' + response.responseText);
                    }
                },
                params: {
                    'uid': Ext.encode( record.get("uid") )
                }
            });
        }


        var toolbarDeleteClick = function(record) {
            var callback = function() {
                Ext.Ajax.request({
                    url: TQ.getAjaxController('delete'),
                    callback: function(options, success, response) {
                        if (response.responseText === '1') {
                            // reload the records and the table selector
                            me.reloadAction();
                        } else {
                            alert('ERROR: ' + response.responseText);
                        }
                    },
                    params: {
                        'uid': Ext.encode( record.get("uid") )
                    }
                });
            }

            TQ.confirmDelete( record.get("title"), callback);
        }


        var grid = new Ext.grid.GridPanel({
            store: this.store,
            loadMask: true,
            columns: [
                {
                    id       : 'uid',
                    header   : TQ.translate('db.uid'),
                    width    : 5,
                    sortable : true,
                    dataIndex: "uid"
                },{
                    id       : "title",
                    header   : TQ.translate('db.title'),
                    width    : "auto",
                    sortable : true,
                    dataIndex: "title",
                    renderer : me.renderTitle
                },{
                    id       : "toolbar",
                    header   : '',
                    width    : 15,
                    sortable : false,
                    resizable: false,
                    menuDisabled: true,
                    hideable: false,
                    renderer : me.renderToolbar,
                    TQOnClick: function(record, fieldName, fieldId, col, data, e) {
                        switch(e.TQ.toolbarAction) {
                            case 'show':
                                return toolbarShowClick(record);
                                break;

                            case 'edit':
                                return toolbarEditClick(record);
                                break;

                            case 'preview':
                                return toolbarPreviewClick(record);
                                break;

                            case 'hide':
                                return toolbarHideClick(record);
                                break;

                            case 'unhide':
                                return toolbarUnhideClick(record);
                                break;

                            case 'delete':
                                return toolbarDeleteClick(record);
                                break;
                        }
                    }
                }
            ],
            stripeRows: true,
            autoExpandColumn: 'title',
            height: 350,
            width: '98%',
            frame: true,
            border: true,
            viewConfig: {
                forceFit: true,
                listeners: {
                    refresh: function(view) {
                        if (!Ext.isEmpty(TQ.getConfiguration('criteriaFulltext'))) {
                            view.el.select('.x-grid3-body .x-grid3-cell').each(function(el) {
                                TQ.highlightText(el.dom, TQ.getConfiguration('criteriaFulltext'));
                            });
                        }
                    }
                }
            },
            tbar: [
                {
                    xtype: 'button',
                    id: 'createButton',
                    text: TQ.icon("button.create")+" "+TQ.translate("button.create"),
                    handler: function() {
                        me.createAction();
                    }
                },
                '->',{
                    xtype: 'textfield',
                    id: 'searchFulltext',
					width: 100,
                    fieldLabel: TQ.translate('label.search.fulltext'),
                    emptyText : TQ.translate('label.empty.fulltext'),
                    listeners: {
                        specialkey: function(f,e){
                            if (e.getKey() == e.ENTER) {
                                me.filterAction(this);
                            }
                        }
                    }
                },
                {xtype: 'tbspacer', width: 10},
                {
                    xtype: 'button',
                    id: 'filterButton',
                    text: TQ.icon("button.search"),
                    handler: function() {
                        me.filterAction();
                    }
                }
            ],
            bbar: [
                {
                    id: 'recordPaging',
                    xtype: 'paging',
                    store: this.store,
                    pageSize: TQ.getConfiguration('limitCount'),
                    displayInfo: true,
                    displayMsg: TQ.translate('label.pager.result'),
                    emptyMsg: TQ.translate('label.pager.empty')
                }
            ]
        });

        return grid;
    },


    filterAction: function(ob, cmd) {
        TQ.Backend.SlideshowGrid.superclass.filterAction.call(this, ob, cmd);
    },

    renderTitle: function(value, metaData, record, rowIndex, colIndex, store) {
        var ret= Ext.util.Format.htmlEncode(value);

        var clientLine  = record.get('_client');
        var contactLine = record.get('_contact');
        var clientLock  = record.get('_clientLocked');

        if( !Ext.isEmpty(contactLine) ) {
            ret += '<div class="contact">'+TQ.translate('label.contact.article')+" "+Ext.util.Format.htmlEncode(contactLine)+'</div>';
        }

        if( !Ext.isEmpty(clientLine) ) {
            var clientOthers = "";
            ret += '<div class="contact">'+TQ.translate('label.contact.client')+" "+Ext.util.Format.htmlEncode(clientLine)+clientOthers+'</div>';
        }

        var clientLock = record.get('_clientLocked');
        if( !Ext.isEmpty(clientLock) && clientLock >= 1 ) {
            ret += '<div class="locked"> '+TQ.translate('label.contact.locked.reason')+": "+TQ.getConfiguration("reasonLockList")[clientLock]+"</div>";
        }

        var locked = record.get('locked');
        if( !Ext.isEmpty(locked) && locked >= 1 ) {
            ret += '<div class="locked"> '+TQ.translate('label.article.locked.reason')+": "+TQ.getConfiguration("reasonLockList")[locked]+"</div>";
        }

        return ret;
    },

    renderType: function(value, metaData, record, rowIndex, colIndex, store) {
        var ret = Ext.util.Format.htmlEncode( TQ.translate("db.type."+value) );

        return ret;
    },

    renderToolbar: function(value, metaData, record, rowIndex, colIndex, store) {
        var  ret = '';

        var deleted = record.get('deleted');

        if( deleted == 0 ) {
            // Edit
            ret += TQ.icon("toolbar.edit");

            // Hidden
            if( record.get("hidden") == 1 ) {
                ret += TQ.icon("toolbar.unhide");
            } else {
                ret += TQ.icon("toolbar.hide");
            }

            // Delete
            ret += TQ.icon("toolbar.delete");
        } else {
            // Show
            ret += TQ.icon("toolbar.show");

        }

        return ret;
    }


});