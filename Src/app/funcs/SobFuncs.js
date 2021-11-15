
    function SobObjectClick(selectedRecord) {
        console.log(new Date() + ' ' + arguments.callee.name+ '()');
        /*
        1. показываем загруженную инфу из таблицы в форме
        2. подгружаем в форму дополнительные данные (описание...)
        */
        var ObjectId     = selectedRecord.id;
        var OwnerObjInfo = Ext.ComponentQuery.query('#OwnerObjInfo')[0];
        var OwnerObjPhone= Ext.ComponentQuery.query('#OwnerObjPhone')[0];
        var OwnerObjDescr= Ext.ComponentQuery.query('#OwnerObjDescr')[0];
        var OwnerObjCommentsText = Ext.ComponentQuery.query('#OwnerObjCommentsList')[0];


        // устанавливаем id объекта (для формы комментов)
        GlobVars.Temp.OpenedSobObject = ObjectId;// todo переделать на какое-то поле в форме?
        //Ext.ComponentQuery.query('#OwnerObjectId')[0].update(ObjectId);

        if (selectedRecord.Metro.length > 0) {
            var Metro = 'м. '+selectedRecord.Metro+ '<br>';
        } else {
            var Metro = '';
        }

        var OwnerObjInfo_Text = selectedRecord.Address + '<br>' +
                                Metro +
                                'Этажность: '+selectedRecord.Floors + '<br>' +
                                'Комнат: '+selectedRecord.FlatType + '<br>' +
                                'Площадь: '+selectedRecord.Square + '<br>' +
                                'Цена: '+selectedRecord.Price + '<br>';

        OwnerObjInfo.update( OwnerObjInfo_Text );
        OwnerObjPhone.update( selectedRecord.Phone + GlobVars.Design.Spinner);
        OwnerObjDescr.update( GlobVars.Design.Spinner ); // "ожидательная крутилка" до заполнения данными
        OwnerObjCommentsText.update( GlobVars.Design.Spinner );


        Ext.Ajax.request({
            url : OwnersGetObject,
            params  : {
                ObjectId  : ObjectId
            },
            success: function(response, opts) {
                var obj = Ext.decode(response.responseText);
                // картинка портала
                var st = obj.SourceType;
                var HtmlTail = '" target="_blank" class="TextOnWhiteBg">источник</a></span>';
                if(st     == 'avito') {OwnerObjPhone.update(obj.Phones + ' <span title="Avito"><img src="'+GlobVars.Design.Avito+'" height="25px"> <a href="https://www.avito.ru' + obj.Link + HtmlTail);}
                else if(st == 'cian') {OwnerObjPhone.update(obj.Phones + ' <span title="Циан"><img src="'+GlobVars.Design.Cian+'" height="25px"> <a href="' + obj.Link + HtmlTail);}
                else if(st == 'sob')  {OwnerObjPhone.update(obj.Phones + ' <span title="Sob"><img src="'+GlobVars.Design.Sob+'" height="25px"></span>');}
                else if(st == 'irr')  {OwnerObjPhone.update(obj.Phones + ' <span title="Irr"><img src="'+GlobVars.Design.Irr+'" height="25px"> <a href="' + obj.Link + HtmlTail);}
                else { OwnerObjPhone.update( obj.Phones + ' ' + obj.Link ); }

                OwnerObjDescr.update( obj.About );
                UpdateSobCommentsContainer(ObjectId, obj.CommentsList)

            },
            failure: function(response, opts) {
                //todo ??
                //Op_ErrorStop('ошибка там-то....');
            }
        });
    }

    function UpdateSobCommentsContainer(ObjectId, text) {
        // вставляем готовый текст или обновлем весь список комментов
        var OwnerObjCommentsList = Ext.ComponentQuery.query('#OwnerObjCommentsList')[0];
        Ext.ComponentQuery.query('#OwnerObjCommentsText')[0].setValue('');          // сбрасываем поле
        Ext.ComponentQuery.query('#OwnerObjCommentsAddBtn')[0].setDisabled(false);  // открываем кнопку
        if(typeof text !== "undefined" && text.length > 0) {
        //if(text.length > 0) {
            OwnerObjCommentsList.update(text); // обновляем комменты
        } else {
            OwnerObjCommentsList.update('');
            //OwnerObjCommentsList.update('нет комментариев');
            Ext.Ajax.request({
                url     : OwnersGetComments,
                params  : {
                    ObjectId     : GlobVars.Temp.OpenedSobObject
                },
                success: function(response, opts) {
                    var obj = Ext.decode(response.responseText);
                    OwnerObjCommentsList.update(obj.CommentsList);
                },
                failure: function(response, opts) {
                    //todo ??
                }
            });
        }
        Ext.ComponentQuery.query('#OwnerObjCommentsContainer')[0].setDisabled(false); // открываем кнопку и поле ввода
    }

    function BuildSobExportUrlString(MainAjaxDriver, ChosenDate) {
        var url = MainAjaxDriver + '?' +
            '&' + OwnersGridProxyParams+
            '&ChosenDate='  + ChosenDate+
            '&SobListNeededDate='  + ChosenDate+
            '&page=1&limit=1000'+
            '&DownloadType=xls';

        return url;
    }

    /*function BuildObjectExportBtnUrlString(MainAjaxDriver, Action, Active, OnlyUserId, DownloadType, SortColumn, SortDir) {
        // кнопка экспорта в Excel
        var url = MainAjaxDriver + '?' +
            '&Action='      + Action +
            '&Active='      + Active +
            '&OnlyUserId='  + OnlyUserId +
            '&DownloadType='+ DownloadType;
        if(typeof SortColumn !== "undefined") { //#COLUMNSORTING// готовим параметры сортировки для совместимости с serverside сортировкой
            var SortObj         = new Object();
            SortObj.property    = SortColumn;
            SortObj.direction   = SortDir;
            url = url + '&sort='+JSON.stringify( Array(SortObj) );
        }
        console.log( arguments.callee.name + '('+Action+'): ' + url);
        return url;
    }*/

