Ext.define('crm.view.Settings.SettingsWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.SettingsWindow',
    id      : "SettingsWindow",
    title   : 'Системные настройки',
    autoShow: true,
    autoScroll: true,
    height  : 300,
    width   : 650,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    initComponent: function() {
        this.items = [
            Ext.create('crm.view.Settings.SettingsForm')
        ];
        this.callParent(arguments);
    }


});
