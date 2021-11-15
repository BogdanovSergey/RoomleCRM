Ext.define('crm.view.Settings.StructureWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.StructureWindow',
    id      : "StructureWindow",
    title   : 'Структура компании, права доступа',
    autoShow: true,
    autoScroll: true,
    height  : 570,
    width   : 800,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    initComponent: function() {
        Ext.apply(this, {
            id          : "StructureWindow",
            items : [
                {
                    xtype : 'StructureTabs'
                }
            ]});


            //Ext.create('crm.view.Settings.SettingsForm')

        this.callParent(arguments);
    }


});
