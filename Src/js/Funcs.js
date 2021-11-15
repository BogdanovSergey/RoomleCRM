function WhileOpeningTheObject(id) {
    // функции при открытии и изменении объекта
    var ObjectForm = Ext.getCmp('ObjectForm');
    var ObjectTabs = Ext.widget('ObjectTabs');
    ObjectForm.getForm().reset();   // сбрасываем предыдущую форму
    ObjectTabs.setActiveTab(0);     // переключаемся на вкладку "Характеристики"
    ChangeObjectFormToDefaultView();
}


function SubmitObjectForm(ObjectWindowName, ObjectTabsName, ObjectFormName, ObjectsGridName, ObjectAdditionsFormName, NeedToClose) {
    console.log(new Date() + ' '+arguments.callee.name + '('+ObjectWindowName+'...)');
    var ObjectForm = Ext.getCmp(ObjectFormName);
    ObjectForm.getForm().submit({
        submitEmptyText : false, // todo не работает ???
        waitMsg : 'Идет отправка...',
        success: function(form, action) {
            var ObjectWindow = Ext.getCmp(ObjectWindowName);

            // Добавляем/обновляем в форму значение Latitude (только с ним работает кнопка "показать на карте")
            //if(typeof action.result.Latitude !== "undefined") {
                ObjectForm.down('#Latitude').setValue(action.result.Latitude);
            //}

            if(action.result.LoadedObjectId > 0) {
                // Объект добавлен
                // обновляем заголовок окна
                ObjectWindow.setTitle(Words_EditObjectTitle + ' №' + action.result.LoadedObjectId);

                // добавляем/обновляем в формы id нового/существующего объекта !!!!
                ObjectForm.down('#LoadedObjectId').setValue(action.result.LoadedObjectId);
                // id для второй формы
                Ext.getCmp(ObjectAdditionsFormName).down('#LoadedObjectId').setValue(action.result.LoadedObjectId); //#DBL

                // Открываем вкладку "Фотографии"
                // Меняем url, обновляем превьюшки по выбранному объекту
                UpdateObjectImagesDataView( action.result.LoadedObjectId, ObjectTabsName, GlobVars.OpenedRealtyType );
                //открываем вкладку "Дополнительно"
                Ext.getCmp(ObjectTabsName).down('#'+ObjectAdditionsFormName).setDisabled(false);
                // открываем кнопку "посмотреть на карте"
                Ext.ComponentQuery.query('#GeoWinBtn')[0].setDisabled(false);

                if(CheckUserAccessRule('Objects-My-EditSpecial')) {
                    // если указано спец право, то пользователь теперь может редактировать только некоторые поля
                    InitUserAccessRights(); // скрыть все поля, оставить только спец
                }

            }

            // Окно успеха
            Ext.Msg.alert('Успех',  action.result.message);
            // TODO сделать обновление грида опциональным (для слабого канала - не требуется)
            Ext.getCmp(ObjectsGridName).getStore().removeAll(); // TODO сделать обновление одной строки, а не всего грида!!!!
            Ext.getCmp(ObjectsGridName).getStore().load();// обновляем весь грид
            if(NeedToClose) {
                ObjectWindow.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                case Ext.form.action.Action.CLIENT_INVALID:
                    Ext.Msg.show({
                        title   :'Ошибка',
                        msg     : 'Пожалуйста, заполните все обязательные поля!',
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.ERROR
                    });
                    break;
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.show({
                        title   :'Ошибка SERVER_INVALID:',
                        msg     : action.result.message,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.ERROR
                    });
            }
        }
    });
}

function SubmitAdditionalForm(ObjectFormName, ObjectTabsName, ObjectWindowName, NeedToClose) {
    var ObjectForm = Ext.getCmp(ObjectFormName);
    ObjectForm.getForm().submit({
        waitMsg:'Дополнительная информация сохраняется...',
        success: function(form, action) {
            var ObjectWindow = Ext.getCmp(ObjectWindowName);
            console.log(action);
            if(action.result.LoadedObjectId > 0) {

            }
            Ext.Msg.alert('Успех',  action.result.message);

            if(NeedToClose) {
                ObjectWindow.close();
            }
        },
        failure: function(form, action) {
            switch (action.failureType) {
                case Ext.form.action.Action.CLIENT_INVALID:
                    Ext.Msg.alert('Ошибка', 'Пожалуйста, заполните все обязательные поля!');
                    break;
                case Ext.form.action.Action.CONNECT_FAILURE:
                    Ext.Msg.alert('Ошибка', 'CONNECT_FAILURE: проблема со связью');
                    break;
                case Ext.form.action.Action.SERVER_INVALID:
                    Ext.Msg.alert('Ошибка', 'SERVER_INVALID: ' + action.result.message );  // TODO будет хорошо выводить внутренности ошибки напр. через JSON.stringify()
            }
        }
    });
}

function UpdateObjectImagesDataView(ObjectId, ObjectTabsName, RealtyType) { //todo ObjectTabsName сократить
    //открываем вкладку "Фотографии"
    console.log('UpdateObjectImagesDataView(): ObjectId: '+ObjectId+' ObjectTabsName:'+ ObjectTabsName + ' RealtyType:' + RealtyType);
    var ObjectTabs = Ext.getCmp(ObjectTabsName);
    var PhotosTab  = GlobVars.NamesObj[RealtyType].PhotosTab; // берем название таба
    //console.log('PhotosTab: '+PhotosTab);
    //console.log(ObjectTabs);
    ObjectTabs.down('#'+PhotosTab).setDisabled(false);

    // Меняем url, обновляем превьюшки по выбранному объекту
    console.log('UpdateObjectImagesDataView(' + ObjectId + ', ' + RealtyType + ')');
    //console.log( Ext.widget('ObjectDataView') );
    //console.log( Ext.getCmp('ObjectDataView') );
    var DataView = Ext.widget('ObjectDataView');
    if(ObjectId > 0) {
        DataView.getStore().proxy.url = GetObjectImagesUrl + ObjectId; // обновляем параметры списка фоток
    } else {
        alert('UpdateObjectImagesDataView(): пустой ObjectId');
    }

    var uploadButton = Ext.ComponentQuery.query('ObjectUploadButton')[0];   // Обновляем url КНОПКИ для загрузки фоток на объект (если она активна)
    if(typeof uploadButton !== "undefined") {
        uploadButton.uploader.url = MainSiteUrl + ImagesUploadUrl + '&ObjectId=' + ObjectId;
    }
    //uploadButton = Ext.widget('ObjectUploadButton');
    //uploadButton.settings.url = MainSiteUrl + ImagesUploadUrl + ObjectId + '&wwwwwwwwwwwwwwwwwwwwww';
   //uploadButton.uploader.url = '12121221';
    //console.log( uploadButton );

/*    var uploadButton = Ext.ComponentQuery.query('ObjectUploadButton')[0],
        uploader = uploadButton.uploader;//.uploader;
    console.log( uploadButton );
    //console.log( Ext.widget('ObjectUploadButton') );
    uploadButton.settings.url = MainSiteUrl + ImagesUploadUrl + ObjectId; // Меняем url в компоненте-загрузчике
    alert(MainSiteUrl + ImagesUploadUrl + ObjectId);
    uploadButton.uploader.url = MainSiteUrl + ImagesUploadUrl + ObjectId+'wwwwwwwwwwwwwwwwwwwwwww'; // Меняем url в компоненте-загрузчике
    //console.log( uploadButton );
    console.log( uploadButton.uploader.url );
*/
    //console.log( DataView );
    //DataView.store.load();
    //console.log( ' DataViewDataViewDataViewDataView ');
    DataView.getStore().load();             // обновляем грид
}

function findColumnIndex(columns, dataIndex) {
    // TODO как переделать проще ~ на "get dataIndex" ?
    var index;
    for (index = 0; index < columns.length; ++index) {
        if (columns[index].dataIndex == dataIndex) { break; }
    }
    return index == columns.length ? -1 : index;
}



function DeleteUploadedImage(ImageId) {
    Ext.Msg.confirm('Удаление фотографии', 'Вы действительно хотите удалить фотографию №' + ImageId + ' ?',
        function(btn) {
            if (btn == 'yes') {
                // process text value and close...

                Ext.Ajax.request({
                    url: DeleteObjectImageUrl,
                    params: {
                        ImageId : ImageId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // фотка удалилась, обновляем сторэдж
                            Ext.ComponentQuery.query('#ObjectDataView')[0].getStore().load();
                        } else {
                            alert(Words_SystemErrorMsg + '101' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        alert(Words_SystemErrorMsg + '100' + '\n' + Words_CallProgrammerMsg);
                    }
                });

            }
        }

    );
}


function SetObjectFirstImage(ImageId) {
    // делаем фотку первой в списке
    Ext.Ajax.request({
        url: SetObjectFirstImageUrl,
        params: {
            ImageId : ImageId
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if(obj.success == true) {
                // обновляем сторэдж
                Ext.ComponentQuery.query('#ObjectDataView')[0].getStore().load();
            } else {
                alert(Words_SystemErrorMsg + '101' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
            }
        },
        failure: function(response, opts) {
            alert(Words_SystemErrorMsg + '100' + '\n' + Words_CallProgrammerMsg);
        }
    });
}

function ArchivateObjectById(rowIndex, ObjectId, GridStoreName, Street) {
    Ext.Msg.confirm('Архивация объекта', 'Отправить в архив объект № <b>' + ObjectId + '</b> (' + Street + ') ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: ArchivateObjectUrl,
                    params  : {
                        ObjectId: ObjectId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup(GridStoreName ); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.removeAt(rowIndex); // спрятать строку TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.load(); // TODO ПОКА СТРОЧКА ПРОСТО НЕ УДАЛЯЕТСЯ, ПЕРЕГРУЖАЕМ СТОРЭДЖ целиком
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

function RestoreObjectById(rowIndex, ObjectId, GridStoreName, Street) {
    Ext.Msg.confirm('Восстановление объекта', 'Восстановить из архива объект № <b>' + ObjectId + '</b> (' + Street + ') ?',
        function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: RestoreObjectUrl,
                    params  : {
                        ObjectId: ObjectId
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            // TODO если выбрана настройка "быстрый инет" - обновлять сторэдж, иначе не обновлять
                            var stor = Ext.data.StoreManager.lookup(GridStoreName); // TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.removeAt(rowIndex); // спрятать строку TODO - НЕ РАБОТАЕТ??? ПОЧИНИТЬ
                            stor.load(); // TODO ПОКА СТРОЧКА ПРОСТО НЕ УДАЛЯЕТСЯ, ПЕРЕГРУЖАЕМ СТОРЭДЖ целиком
                        } else {
                            alert(Words_SystemErrorMsg + '103' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        alert(Words_SystemErrorMsg + '102' + '\n' + Words_CallProgrammerMsg);
                    }
                });

            }
        }

    );
}


