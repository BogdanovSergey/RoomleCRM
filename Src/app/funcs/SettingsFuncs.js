function SubmitPriceForm(NeedToClose) {
    var AdPricesForm = Ext.getCmp('AdPricesForm');

    AdPricesForm.getForm().submit({
        waitMsg:'Идет отправка...',
        success: function(form, action) {
            Ext.Msg.show({
                title   :'Успех',
                msg     : action.result.message,
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.OK
            });
            if(NeedToClose) {
                var AdPricesWindow = Ext.getCmp('AdPricesWindow');
                AdPricesWindow.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.alert('Ошибка', 'SERVER_INVALID: ' + action.result.message);
            }
        }
    });
}


function SubmitSettingsForm(NeedToClose) {
    var SettingsForm = Ext.getCmp('SettingsForm');

    /*Ext.getCmp('SettingsForm').on({
        close: {fn: function() {
            alert(1222);
        }, scope: this, single: true}
        //mouseover: {fn: panel.onMouseOver, scope: panel}
    });*/


    SettingsForm.getForm().submit({
        waitMsg:'Идет отправка...',
        success: function(form, action) {
            var SettingsWindow = Ext.getCmp('SettingsWindow');
            //Ext.Msg.alert('Успех',  action.result.message);
            Ext.Msg.show({
                title   :'Успех',
                msg     : action.result.message,
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.OK
            });
            if(NeedToClose) {
                SettingsWindow.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                /*case Ext.form.action.Action.CLIENT_INVALID:
                    Ext.Msg.alert('Ошибка', 'Пожалуйста, заполн');
                    break;*/
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.alert('Ошибка', 'SERVER_INVALID: ' + action.result.message);
            }
        }
    });
}

function LoadSettingsForm() {
    var SettingsForm = Ext.getCmp('SettingsForm');
    SettingsForm.getForm().load({
        //waitMsg : 'Открывается объект № ' + selectedRecord.data.id,
        url     : 'Super.php',
        method  : 'GET',
        params  : {
            //id  : selectedRecord.data.id,
            Action: 'LoadSettingsForm'
        },
        success: function(response, options) {
            //var data = options.result.data;

        }
    });
}

function AddNewPositionOrGroupOrStatus(Type, text) {
    if(Type == 'position') {
        var url     = AddNewPositionUrl;
        var tree    = 'PositionsTreePanel';
        var field   = 'PositionId';
    } else if(Type == 'group') {
        var url     = AddNewGroupUrl;
        var tree    = 'GroupsTreePanel';
        var field   = 'GroupId';
    } else if(Type == 'status') {
        var url     = AddNewStatusUrl;
        var tree    = 'StatusTreePanel';
        var field   = false;
    } else {
        alert('Type error');
    }
    Ext.Ajax.request({
        url     : url,
        params  : {
            name : text
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            Ext.Msg.alert('Успех',  obj.message);
            Ext.getCmp( tree ).getStore().load();
            if(field) {
                Ext.ComponentQuery.query( '#'+field )[0].reset();
                Ext.ComponentQuery.query( '#'+field )[0].getStore().load();
            }
        },
        failure: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            Ext.Msg.alert('Ошибка',  obj.message);
        }
    });
}


function RenameStrucItem(Type, ItemId, Name) {
    if(Type == 'position') {
        var Header  = 'Изменение названия должности';
        var tree    = 'PositionsTreePanel';
        var field   = 'PositionId';
    } else if(Type == 'group') {
        var Header  = 'Изменение названия отдела';
        var tree    = 'GroupsTreePanel';
        var field   = 'GroupId';
    } else if(Type == 'status') {
        var Header  = 'Изменение названия статуса';
        var tree    = 'StatusTreePanel';
        var field   = false;
    } else {
        alert('Type error: '+Type); // TODO цетрализовать, протоколировать
    }
    Ext.Msg.prompt(Header, '',
        function(btn, ItemNewName) {
            if (btn == 'ok') {
                Ext.Ajax.request({
                    url : RenameStrucItemUrl,
                    params  : {
                        ItemType    : Type,
                        ItemId      : ItemId,
                        ItemOldName : Name,
                        ItemNewName : ItemNewName
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            Ext.getCmp( tree ).getStore().load();
                            if(field) {
                                Ext.ComponentQuery.query('#' + field )[0].reset();
                                Ext.ComponentQuery.query('#' + field )[0].getStore().load();
                            }
                            Ext.Msg.show({
                                title   :'Успех',
                                msg     : obj.message,
                                buttons : Ext.Msg.OK
                            });
                        } else {
                            alert(Words_SystemErrorMsg + '103' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        //todo протоколировать ошибку
                        alert(Words_SystemErrorMsg + '102' + '\n' + Words_CallProgrammerMsg);
                    }
                });

            }
        },
        window,
        false,
        Name
    );




}

function DeleteStructureElement(WinCaption, WinText, WinText2, Type, ItemId, BindToItemId, ItemName, TreePanelName, ComboField, BindToType) {
    var Str = new Object(), Str2 = new Object();
    /*if(Type == 'Position') {
        var tree    = 'PositionsTreePanel';
        var field   = 'PositionId';
    } else if(Type == 'Group') {
        var tree    = 'GroupsTreePanel';
        var field   = 'GroupId';
    } else if(Type == 'Right') {

    } else {
        alert('Type error: '+Type); // TODO цетрализовать, протоколировать
    }
*/
    Ext.Msg.confirm(WinCaption, WinText +' <b>' + ItemName + '</b>' + WinText2 + ' ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url : RemoveStructureItemUrl,
                    params  : {
                        ItemType     : Type,
                        ItemId       : ItemId,
                        BindToItemId : BindToItemId,
                        BindToType   : BindToType
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            Ext.getCmp( TreePanelName ).getStore().load();
                            if(ComboField) {
                                Ext.ComponentQuery.query('#' + ComboField)[0].reset();
                                Ext.ComponentQuery.query('#' + ComboField)[0].getStore().load();
                            }
                            Ext.Msg.show({
                                title   :'Успех',
                                msg     : obj.message,
                                buttons : Ext.Msg.OK
                            });
                        } else {
                            alert(Words_SystemErrorMsg + '103' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        //todo протоколировать ошибку
                        alert(Words_SystemErrorMsg + '102' + '\n' + Words_CallProgrammerMsg);
                    }
                });

            }
        }
    );
}

function WelcomeWindowRender() {
    /*
    Ext.Ajax.request({
        url : DataRequestUrl,
        params  : {
            DataType     : 'AdPortalsInfo',
            someItemId   : 0
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if(obj.success == true) {

                var PortalListArr = [];
                var ObjLength = CountObectLength(obj.data);
                Ext.getCmp('WelcomeWindowPanelPortalItems').update(); // убираем крутилку внутри
                for(var i=0; i<ObjLength; i++) {
                    var o = obj.data[i];
                    // добавляем данные портала в массив
                    WelcomeWindowPanelPortalAddItem(o);
                }
            } else {
                alert('WelcomeWindowRender NOT success');
            }
        },
        failure: function(response, opts) {
            //todo протоколировать ошибку
            alert('WelcomeWindowRender failure');
        }
    });
*/
    WelcomeWindowLoadNews();
    WelcomeWindowLoadPayment()

}

function WelcomeWindowLoadNews() {
    // загружаем новости
    Ext.Ajax.request({
        url : DataRequestUrl,
        params  : {
            DataType : 'BriefNews',
            Count    : 5
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if(obj.success == true) {

                var news = Ext.ComponentQuery.query('#WelcomeWindowNewsPanel')[0];
                var ObjLength = CountObectLength(obj.data);
                //Ext.getCmp('WelcomeWindowNewsPanel').update(); // убираем крутилку внутри
                news.update();
                for(var i=0; i<ObjLength; i++) {
                    var o = obj.data[i];
                    // добавляем данные портала в массив
                    //WelcomeWindowPanelPortalAddItem(o);
                    //Ext.getCmp('WelcomeWindowNewsPanel').update(i);
                    news.add(
                        Ext.create('Ext.form.Panel', {
                                border : false,
                                items: {
                                    xtype     : 'fieldcontainer',
                                    hideLabel : true,
                                    items : [
                                        {
                                            //xtype   : 'text',
                                            html    : o.NewsTitle+':',
                                            style   : 'font-weight: bold',
                                            border  : false
                                        }, {
                                            //xtype   : 'text',
                                            style   : 'margin-left: 20px;',
                                            html    : o.NewsText,
                                            border: false
                                        }
                                    ]
                                }


                            }
                        )
                    );
                }
            } else {
                alert('WelcomeWindowRender NOT success');
            }
        },
        failure: function(response, opts) {
            //todo протоколировать ошибку
            alert('WelcomeWindowRender failure');
        }
    });

}


function WelcomeWindowLoadPayment() {
    // загружаем новости
    Ext.Ajax.request({
        url     : DataRequestUrl,
        params  : {
            DataType : 'PaymentInfo'
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if(obj.success == true) {

                var billpanel = Ext.ComponentQuery.query('#WelcomeWindowBillingPanel')[0];
                var ObjLength = CountObectLength(obj.data);
                //Ext.getCmp('WelcomeWindowNewsPanel').update(); // убираем крутилку внутри
                billpanel.update(obj.data.PayedTill);

            } else {
                alert('WelcomeWindowRender NOT success');
            }
        },
        failure: function(response, opts) {
            //todo протоколировать ошибку
            alert('WelcomeWindowRender failure');
        }
    });

}


function WelcomeWindowPanelPortalAddItem(o) {
    Ext.getCmp('WelcomeWindowPanelPortalItems').add(
        Ext.create('Ext.form.Panel', {
            html: o.PortalName + '<br> <img src="' + o.PortalImg + '" height="30" align="center"><br>' + o.PortalStatus,
            height: 80,
            width: 80,
            itemId  : o.PortalId,
            id      : o.PortalId,
            //padding : '0 0 0 5',
            margin  : '5 5 5 5',
            border  : true,
            listeners: {
                mouseover: {
                    element: 'el', //bind to the underlying el property on the panel
                    fn: function () {
                        Ext.getCmp(o.PortalId).setBodyStyle('background', '#CED9E7');
                        //Ext.ComponentQuery.query('#WelcomeWindowPanelDescription')[0].html(o.Description);
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalDescription')[0].update(o.Description);
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalContacts')[0].update("<b>Контакты:</b><br>"+o.Contacts+"");
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalLoadInfoText')[0].update("<b>Выгрузка:</b><br>"+o.LoadInfoText);
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalTechInfoText')[0].update(o.TechInfoText);
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalPrice')[0].update("<b>Цена:</b><br>"+o.Price+"");
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalXmlLink')[0].update("<b>Xml ссылка:</b><br>"+o.XmlLink+"");
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalLoadCount')[0].update("<b>Выгружается:</b><br>"+o.LoadCount+"");
                    }
                },
                mouseout: {
                    element: 'el', //bind to the underlying el property on the panel
                    fn: function () {
                        Ext.getCmp(o.PortalId).setBodyStyle('background', '#ffffff');
                        //Ext.ComponentQuery.query('#WelcomeWindowPanelPortalDescription')[0].html('');
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalDescription')[0].update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalContacts')[0].update();
                        //Ext.getCmp('WelcomeWindowPanelPortalTechInfoText').update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalLoadInfoText')[0].update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalTechInfoText')[0].update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalPrice')[0].update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalXmlLink')[0].update();
                        Ext.ComponentQuery.query('#WelcomeWindowPanelPortalLoadCount')[0].update();
                    }
                }
            }
        })
    );

}

