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

TQ.Backend.AbstractGrid = Ext.extend(Ext.Container, {
    layout: 'fit',

    store:	null,
    grid:	null,

    constructor: function(config) {
        TQ.Backend.AbstractGrid.superclass.constructor.call(this, config);

        this.store = this.createStore();

        if( this.store ) {
            this.grid = this.createGrid();

            if( this.grid ) {
                // Custom on click
                this.grid.on('cellclick', function(grid, rowIndex, colIndex, e) {
                    var fieldId	  = grid.getColumnModel().getColumnId(colIndex);
                    var col		  = grid.getColumnModel().getColumnById(fieldId);

                    if( col.TQOnClick ) {
                        // Fetch add. informations
                        var record 	  = grid.getStore().getAt(rowIndex);
                        var fieldName = grid.getColumnModel().getDataIndex(colIndex);

                        var data	  = record.get(fieldName);

                        // Init TQ customization stuff for event
                        e.TQ = {};
                        e.TQ.toolbarAction = false;

                        // Toolbar "magic" icon click detection
                        if( fieldId.match(/toolbar/g) ) {
                            var srcElement = false;

                            if( e.browserEvent.srcElement ) {
                                srcElement = e.browserEvent.srcElement;
                            } else if(  e.browserEvent.target ) {
                                srcElement = e.browserEvent.target;
                            }

                            if( srcElement ) {
                                var iconClassList = srcElement.getAttribute("class").split(" ");

                                if( iconClassList.indexOf("t3-icon-document-info") >= 0 ) {
                                    e.TQ.toolbarAction = 'show';
                                } else if( iconClassList.indexOf("t3-icon-document-open") >= 0 ) {
                                    e.TQ.toolbarAction = 'edit';
                                } else if( iconClassList.indexOf("t3-icon-document-view") >= 0 ) {
                                    e.TQ.toolbarAction = 'preview';
                                } else if( iconClassList.indexOf("t3-icon-edit-hide") >= 0 ) {
                                    e.TQ.toolbarAction = 'hide';
                                } else if( iconClassList.indexOf("t3-icon-edit-unhide") >= 0 ) {
                                    e.TQ.toolbarAction = 'unhide';
                                } else if( iconClassList.indexOf("t3-icon-edit-delete") >= 0 ) {
                                    e.TQ.toolbarAction = 'delete';
                                } else if( iconClassList.indexOf("t3-icon-status-locked") >= 0 ) {
                                    e.TQ.toolbarAction = 'lock';
                                } else if( iconClassList.indexOf("t3-icon-status-readonly") >= 0 ) {
                                    e.TQ.toolbarAction = 'unlock';
                                }
                            }
                        }

                        // Call handler
                        col.TQOnClick(record, fieldName, fieldId, col, data, e);
                    }
                });

                this.add(this.grid);
            }
        }
    },


    createStore: function() {

    },

    createGrid: function() {

    },

    /**************************************************************************
     * Action methods
     *************************************************************************/

    filterAction: function(ob, cmd) {
        TQ.setConfiguration('criteriaFulltext', Ext.getCmp('searchFulltext').getValue() );

        this.store.reload();
    },

    createAction: function(ob, cmd) {

    },

    reloadAction: function(ob, cmd) {
        this.filterAction();
    },

    /**************************************************************************
     * Render methods
     *************************************************************************/

    rendererUrl: function(value, metaData, record, rowIndex, colIndex, store) {
        value = Ext.util.Format.htmlEncode(value);
        var qtip = Ext.util.Format.htmlEncode(value);
        return '<div ext:qtip="' + qtip +'">' + value + '</div>';
    },

    rendererDatetime: function(value, metaData, record, rowIndex, colIndex, store) {
        var dateToday		= new Date().format("Y-m-d");
        var dateYesterday	= new Date().add(Date.DAY, -1).format("Y-m-d");

        var ret = Ext.util.Format.htmlEncode(value);
        var qtip = Ext.util.Format.htmlEncode(value);

        ret = ret.split(dateToday).join('<strong>'+TQ.Backend.Module.conf.lang.today+'</strong>');
        ret = ret.split(dateYesterday).join('<strong>'+TQ.Backend.Module.conf.lang.yesterday+'</strong>');

        return '<div ext:qtip="' + qtip +'">' + ret + '</div>';
    },

    renderToolbar: function(value, metaData, record, rowIndex, colIndex, store) {
        return '';
    },

    renderStatus: function(value, metaData, record, rowIndex, colIndex, store) {
        var ret = TQ.translate('status.'+value);

        return ret;
    }


});