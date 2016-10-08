function showProvince(){
	var list = eval(json_provice);
	var html = '';
	$.each(list,function(i,item){
		html += ' <li onclick="showCityOfProvince(\''+item.code+'\''+','+'\''+item.name+'\')"><a href="javascript:;">'+item.name+'</a></li>'
	});
	$('#provinceDiv').append(html);
	showhidediv('province2');
}

function showCityOfProvince(p_code,p_name){		
	document.getElementById('province').value=p_name;
	var city_list = eval(json_city);
	var city_html = '';	
		//alert(pcode.substr(0,2));
	$.each(city_list,function(i,item){
		if(item.code.substr(0,2) == p_code.substr(0,2)){
			city_html += ' <li onclick="showDistrictOfCity(\''+item.code+'\''+','+'\''+item.name+'\')"><a href="#">'+item.name+'</a></li>';
		}
	});
	$('#cityDiv').empty();
	$('#cityDiv').append(city_html);
	showhidediv('city2');
}

function showDistrictOfCity(city_code,city_name){
	document.getElementById('city').value=city_name;
	var district_list = eval(json_district);
	var district_html = '';
		//alert(pcode.substr(0,2));
	$.each(district_list,function(i,item){
		if(item.code.substr(0,4) == city_code.substr(0,4)){
			//district_html += ' <li><a href="#">'+item.name+'</a></li>';
			district_html += ' <li onclick="okProvinceCity(\''+item.code+'\''+','+'\''+item.name+'\')"><a href="#">'+item.name+'</a></li>';
		}
	});
	$('#districtDiv').empty();
	$('#districtDiv').append(district_html);
	showhidediv('district2');
}

function okProvinceCity(d_code,d_name){
	document.getElementById('district').value=d_name;
	showhidediv('province2');
	showhidediv('city2');
	showhidediv('district2');	
}

function backdiv(whereNum){
	if(whereNum == 1){
		showhidediv('province2');
	}else if(whereNum == 2){
		showhidediv('city2');
	}else if(whereNum == 3){
		showhidediv('district2');
	}
}

//创建一个showhidediv的方法，直接跟ID属性  
function showhidediv(id){  
	var sbtitle=document.getElementById(id);  
	if(sbtitle){  
	   if(sbtitle.style.display=='block'){  
			sbtitle.style.display='none';  
	   }else{  
			sbtitle.style.display='block';  
	   }  
	}  
}  
