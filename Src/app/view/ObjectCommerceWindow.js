Ext.define('crm.view.ObjectCommerceWindow', {
    extend      : 'Ext.window.Window',
    alias       : 'widget.ObjectCommerceWindow',
    id          : "ObjectCommerceWindow",
    //title       : 'Words_CreateObjectTitle',
    layout      : 'fit',
    resizable   : true,

    minWidth    : 850,
    minHeight   : 400,
    maxHeight   : 1200,
    //TODO добавить скроллы
    //autoScroll  : true,
    closeAction : 'destroy',
    constrain   : true,
    modal       : true,

    initComponent: function() {
        // todo сделать динамичную высоту
        //var WinHeight = Ext.getCmp.getHeight();
        //Ext.getCmp('MailListWindow_List').setHeight( WinHeight );
        //alert(WinHeight);


        Ext.apply(this, {
            maxHeight   : 620,
            id          : "ObjectCommerceWindow",
            items : [
                {
                    xtype : 'ObjectCommerceTabs'
                }
            ]});
        this.callParent(arguments);


        /*this.items = [
            Ext.create('Ext.tab.Panel', {
                id      : "ObjectCommerceTabs",
                //alias   : "widget.ObjectTabs",
                plain   : true,
                items   : [
                    Ext.getCmp('ObjectCommerceForm')
                    //ObjectForm,
                    //ObjectPhotosTab
                    //Ext.getCmp('crm.view.ObjectPhotosTab')
                ]
            })

        ];
        this.callParent(arguments);*/
    }

    // items       : ObjectTabs
});