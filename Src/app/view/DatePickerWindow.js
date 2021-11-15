Ext.define('crm.view.DatePickerWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.DatePickerWindow',
    id      : "DatePickerWindow",
    title   : 'Выбор даты',
    header  : false,
    autoShow: true,
    autoScroll: true,
    height  : 200,
    width   : 200,
    layout  : 'fit',
    //modal       : true,
    //constrain   : true,
    bodyBorder  : false,
    border      : false,
    closeAction : 'destroy',

    initComponent: function() {
        this.items = [

            Ext.create('Ext.panel.Panel', {
                //title: 'Choose a future date:',
                width: 200,
                bodyPadding: 0,
                //renderTo: Ext.getBody(),
                items: [{
                    xtype: 'datepicker',
                    maxDate: new Date(),
                    handler: function(picker, date) {
                        // do something with the selected date
                    }
                }]
            })

        ];
        this.callParent(arguments);
    }



});