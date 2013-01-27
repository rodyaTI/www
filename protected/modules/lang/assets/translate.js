
function translateField(field, fromLang, isImperavi, modelName){
	isImperavi = isImperavi || 0;
	
    var fields = new Object();
    for(var key in activeLang){
        var lang = activeLang[key];
		if (isImperavi == 1) {
			fields[lang] = eval(modelName+'_'+field+'_'+lang).getCodeEditor()
		} else {
			fields[lang] = $('#id_'+field+'_'+lang).val();
		}
        //alert(fieldId+' = '+fields[lang]);
    }

    if(!fields[fromLang]){
        error(errorNoFromLang);
        return false;
    }

    var postData = new Object();
    postData['fromLang'] = fromLang;
    postData['fields'] = fields;

    $.ajax({
        type: "POST",
        url: baseUrl+'/lang/main/ajaxTranslate',
        data: postData,
        dataType: 'json',
        success: function(msg){
            if(msg.result == 'ok'){
                message(successTranslate, 'message', 2000);
                for(var lang in msg.fields){
                    var val = msg.fields[lang];
                    if (isImperavi == 1) {
						eval(modelName+'_'+field+'_'+lang).setCodeEditor(val);
					} else {
						$('#id_'+field+'_'+lang).val(val);
					}
                }
            }else{
                error(errorTranslate);
            }
        }
    });
}

function copyField(field, fromLang, isImperavi, modelName){
	isImperavi = isImperavi || 0;
	
    var copyValue = (isImperavi == 1) ? eval(modelName+'_'+field+'_'+fromLang).getCodeEditor() : $('#id_'+field+'_'+fromLang).val() ;


    for(var key in activeLang){
        var lang = activeLang[key];
        
        if(fromLang != lang){
			if (isImperavi == 1) {
				eval(modelName+'_'+field+'_'+lang).setCodeEditor(copyValue);
			} else {
				$('#id_'+field+'_'+lang).val(copyValue);
			}
            
        }
    }
    message(successCopy);
}