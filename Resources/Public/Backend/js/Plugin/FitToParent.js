/* taken from powermail, thanks */

/* plugin for resize of grid in single container */
Ext.namespace('Ext.ux.plugin');
Ext.ux.plugin.FitToParent = Ext.extend(Object, {
    constructor : function(parent) {
        this.parent = parent;
    },
    init : function(c) {
        c.on('render', function(c) {
            c.fitToElement = Ext.get(this.parent
                    || c.getPositionEl().dom.parentNode);
            if (!c.doLayout) {
                this.fitSizeToParent();
                Ext.EventManager.onWindowResize(this.fitSizeToParent, this);
            }
        }, this, {
            single : true
        });
        if (c.doLayout) {
            c.monitorResize = true;
            c.doLayout = c.doLayout.createInterceptor(this.fitSizeToParent);
        }
    },
    fitSizeToParent : function() {
        var marginTop    = 5;
        var marginLeft   = 15;
        var marginRight  = 15;
        var marginBottom = 15;

        // Uses the dimension of the current viewport, but removes the document header
        // and an additional margin of 40 pixels (e.g. Safari needs this addition)

        this.fitToElement.setStyle("marginTop", marginTop+"px");
        this.fitToElement.setStyle("marginLeft", marginLeft+"px");
        this.fitToElement.setStyle("marginRight", marginRight+"px");

        //this.fitToElement.setHeight(Ext.getBody().getHeight() - this.fitToElement.getTop() - 40);
        this.fitToElement.setHeight(Ext.getBody().getHeight() - this.fitToElement.getTop() - marginBottom );
        var pos = this.getPosition(true)
        var size = this.fitToElement.getViewSize();
        this.setSize(size.width - pos[0], size.height - pos[1]);
    }
});