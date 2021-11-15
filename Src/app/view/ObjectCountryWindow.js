Ext.define('crm.view.ObjectCountryWindow', {
    extend      : 'Ext.window.Window',
    alias       : 'widget.ObjectCountryWindow',
    id          : "ObjectCountryWindow",
    //title       : 'Words_CreateObjectTitle',
    layout      : 'fit',
    resizable   : true,

    minWidth    : 750,
    minHeight   : 400,
    maxHeight   : 1200,
    //TODO добавить скроллы
    closeAction : 'destroy',
    constrain   : true,
    modal       : true,

    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectCountryWindow",
            items : [
                {
                    xtype : 'ObjectCountryTabs'
                }
            ]});
        this.callParent(arguments);

        /*this.items = [
            Ext.create('Ext.tab.Panel', {
                id      : "ObjectCountryTabs",
                //alias   : "widget.ObjectTabs",
                plain   : true,
                items   : [
                    Ext.getCmp('ObjectCountryForm')
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