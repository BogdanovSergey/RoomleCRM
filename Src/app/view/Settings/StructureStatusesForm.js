Ext.define('crm.view.Settings.StructureStatusesForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.StructureStatusesForm',
    title       : 'Управление статусами',
    url         : 'Super.php',
    bodyPadding : 10,
    defaultType : 'textfield',
    initComponent: function() {
        Ext.apply(this, {
            id          : "StructureStatusesForm",
            items   : [
                {
                    xtype       : 'fieldcontainer',
                    hideLabel   : true,
                    layout      : 'hbox',
                    items: [
                        Ext.create('Ext.Button', {
                            iconCls : 'AddCls',
                            text    : 'Создать новый статус',
                            handler : function() {
                                Ext.Msg.prompt('Название', 'Введите название нового статуса:', function(btn, text){
                                    if (btn == 'ok') {
                                        AddNewPositionOrGroupOrStatus('status', text);
                                    }
                                });
                            }
                        })
                    ]
                },
                {
                    xtype   : 'text',
                    width   : 100,
                    padding : '5 5 5 0'
                    //text    : ' '
                },
                Ext.create('Ext.tree.Panel', {
                    id      : 'StatusTreePanel',
                    header  : false,
                    width   : 730,
                    height  : 300,
                    rootVisible: false,
                    store: Ext.create('Ext.data.TreeStore', {
                        model   : 'crm.model.RightsModel',
                        autoLoad: true,
                        proxy: {
                            type    : 'ajax',
                            url     : 'Super.php?Action=GetStatusesStructure',
                            reader  : {
                                type: 'json'
                            }
                        }
                    }),
                    //multiSelect: true,
                    columns: [
                        {
                            dataIndex   : 'id',
                            hidden      : true
                        },
                        {
                            xtype       : 'treecolumn', //this is so we know which column will show the tree
                            text        : 'Название статуса',
                            width       : 550,
                            menuDisabled: true,
                            dataIndex   : 'ItemName'
                            //locked: true
                        },
                        {
                            xtype       : 'actioncolumn',
                            text        : ' ',
                            width       : 30,
                            dataIndex   : 'EditColumn',
                            menuDisabled: true,
                            items   : [{
                                tooltip : 'Изменить название',
                                getClass: function (value, meta, record, rowIndex, colIndex) {
                                    if(record.get('ItemType') == 'Right') {
                                        var cls = 'InvisibleItem';  //ничего не показываем
                                    } else {
                                        var cls = 'EditCls';      // иконка редактирования
                                    }
                                    return cls;
                                },
                                handler:function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                                    RenameStrucItem('status', record.get('id'), record.get('ItemName'));
                                }
                            }]
                        },

                        {
                            xtype       : 'actioncolumn',
                            text        : ' ',
                            width       : 30,
                            dataIndex   : 'DeleteColumn',
                            sortable    : false,
                            items       : [{
                                //tooltip : 'Удалить должность и открепить вложенные права',
                                getClass: function (value, meta, record, rowIndex, colIndex) {
                                    if(record.get('ItemType') == 'Right') {
                                        var cls = 'RemoveRuleCls';  // иконка открепления права доступа
                                    } else {
                                        var cls = 'DeleteCls';      // иконка удаления должности
                                    }
                                    return cls;
                                },
                                handler:function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                                    var rec = grid.getStore().getAt(rowIndex);
                                    if(rec.get('ItemType') === 'Status') {
                                        var descr = rec.get('ItemName');
                                        var WinCaption  = 'Подтвердите удаление статуса';
                                        var WinText     = 'Вы действительно хотите удалить статус с названием: ';
                                        var WinText2    = '';
                                    }/* else if(rec.get('ItemType') === 'Right') {
                                        var descr = rec.get('RightDescr');
                                        var WinCaption  = 'Открепление права доступа';
                                        var WinText     = 'Вы действительно хотите открепить право доступа: ';
                                        var WinText2    = ' от отдела: <b>' + rec.get('BindToItemName') +'</b>';
                                    }*/
                                    DeleteStructureElement(WinCaption, WinText, WinText2, rec.get('ItemType'), rec.get('id'), rec.get('BindToItemId'), descr, 'StatusTreePanel', false, 'status');
                                }
                            }]
                        }
                    ]
                })
            ],
            buttons : [{
                text: 'Закрыть',
                handler: function() {
                    Ext.getCmp('StructureWindow').close();
                }
            }]

        } );
        this.callParent(arguments);
    },
    listeners: {
        close : {
            fn: function() {
                alert(1222);
            },
            element: 'body'
        }
    }

});



