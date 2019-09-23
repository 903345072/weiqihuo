<div class="page-container">
        

<div id="w0" class="list-container cl">
<div class="list-view">
<table class="table table-border table-bordered table-bg table-hover"><thead>
<tr><th class="editable"><a href="/admin/risk/risk?sort=-product.name" data-sort="-product.name">产品名称</a></th><th class="editable"><a href="/admin/risk/risk?sort=-product.risk" data-sort="-product.risk">滑点</a></th><th class="editable">当前价格</th><th class="editable">买涨金额</th><th class="editable">买跌金额</th><th>操作</th></tr>
</thead>
<tbody>
<?php foreach ($productArr as $key => $value): ?>
<tr class="odd" data-key="<?= $key ?>">
	<td><?= $value['name'] ?></td>
	<td><?= $value['risk'] ?></td>
	<td style="    font-weight: bold;color:#333" id="newPrice<?= $value['table_name'] ?>">0</td>
	<td style="    font-weight: bold;color:red"   id="upPrice<?= $value['id'] ?>"><?= $value['upprice'] ?></td>
	<td style="    font-weight: bold;color:green"  id="downPrice<?= $value['id'] ?>"><?= $value['downprice'] ?></td>
	<td style="width: 80px;">
	<a class="giveBtn btn-primary-outline btn radius  size-S" href="/admin/risk/risk?id=<?= $value['id'] ?>">滑点设置</a>
	
	<a class="deleteBtn btn-danger-outline btn radius  size-S" style="margin-top: 5px;" href="/admin/risk/risk?id=<?= $value['id'] ?>">复位</a>
	</td>
	
</tr>
<?php endforeach ?>


</tbody></table>
</div><input type="hidden" class="isAjax" value="1"></div>
<script>
$(function () {
    $(".list-container").on('click', '.giveBtn', function () {
        var $this = $(this);
        $.prompt('请输入滑点', function (value) {
            $.post($this.attr('href'), {risk: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '设置成功', function () {
                        location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
	setInterval("getNewData()",1000);
	setInterval("getTradeData()",1000);
	$(".list-container").on('click', '.deleteBtn', function () {
        var $this = $(this);
       $.post($this.attr('href'), {risk: 0}, function (msg) {
            if (msg.state) {
                $.alert(msg.info || '设置成功', function () {
                    location.reload();
                });
            } else {
                $.alert(msg.info || '设置成功');
            }
        });
        return false;
    });
});


function getTradeData(){
	$.get('/admin/risk/risk1?' + Math.random(), function(newData) {
		for(var index in newData['info']){ 
			$('#upPrice'+newData['info'][index]['id']).html(Number(newData['info'][index]['upprice']));
			$('#downPrice'+newData['info'][index]['id']).html(Number(newData['info'][index]['downprice']));
		}  
		
	});
}
function getNewData(){
	$.get('/price.json?' + Math.random(), function(newData) {
		for(j in newData) {
			$('#newPrice'+j).html(Number(newData[j]));   
		}
			/*//$('#newPricery').html(Number(newData['ry']));
			$('#newPricebtc').html(Number(newData['btc']));
			//$('#newPriceltc').html(Number(newData['ltc']));
			$('#newPricehkhsi').html(Number(newData['hkhsi']));
			$('#newPriceyb').html(Number(newData['yb']));
			$('#newPriceoil').html(Number(newData['oil']));*/
        });
}
</script>    
</div>
