Ext.define('crm.view.UserWindow' ,{
    extend      : 'Ext.window.Window',
    alias       : 'widget.UserWindow',
    id          : "UserWindow",
    title       : 'Сотрудник',
    //autoShow    : true,
    autoScroll  : true,
    //resizable   : true,
    minHeight   : 300,
    minWidth    : 800,
    //layout      : 'fit',
    modal       : true,
    //constrain   : true,
    closeAction : 'destroy',
    initComponent: function() {
        //this.items = [
        Ext.apply(this, {

            id      : 'UserWindow',
            itemId  : 'UserWindow',
            border  : false,
            autoHeight: true,
            //layout  : 'fit',
            //flex    : 1,
            layout  : {
                type    : 'hbox',
                align   : 'stretch'
            }
    } );
        //];
        this.callParent(arguments);
    }



});