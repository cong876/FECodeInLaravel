
$(document).ready(function() {
	myRequire("/js/operator/components/item-modal.vue", afterRequire);
});


$("#activitesMangement").addClass("active");
$(".container").css({"padding-bottom": "0"});

$("#activityPreview").find(".activityImage").height($("#activityPreview").find(".activityImage").width() / 2);

if ($(".activityType").text() == "主题性活动") {
	$("#header").find("section").eq(0).hide();
	$("#header").find(".countDownContainer").hide();
	$("#endTime").removeAttr("readonly");
}

var publish = 0;
var int;


var afterRequire = function () {

	var tags = $("#tags").data("tags");
	var activityId = $("#activityId").text();
	var activityType = $(".activityType").data("type");														// 1 means period activity , 2 means theme activity
	var activityTime = $(".activityStartTime").text();

	var activityItems = $("#items").data("items");
	activityItems = activityItems.map(function(index, i) {
		var indexAfterParse = index;
		indexAfterParse.order = i + 1;
		indexAfterParse.source = 'activityItems';
		if (indexAfterParse.tag_ids) {
			indexAfterParse.tags = tags.filter(function (index) {
				return indexAfterParse.tag_ids.indexOf(index.id.toString()) > -1
			});
		}
		return indexAfterParse;
	});
	var killItems = $("#killItems").data("items");
	killItems = killItems.map(function(index, i) {
		var indexAfterParse = index;
		$.extend(indexAfterParse, index.item);
		indexAfterParse.start_time = indexAfterParse.start_time.substr(11,20);
		indexAfterParse.source = 'killItems';
		return indexAfterParse;
	});

	var newItem = {
		title: "",
		description: "",
		price: "",
		postage: "",
		market_price: "",
		country_id: "--请选择--",
		seller_id: "--请选择--",
		operator_id: "1",
		buy_per_user: 10,
		inventory: 999,
		pic_urls: [],
		tag_ids: [],
		tags: [],
		is_on_shelf: true,
		source: 'activityItems'
	};
	var newKillItem = $.extend({}, newItem);
	newKillItem.postage = 0;
	newKillItem.inventory = 1;
	newKillItem.is_on_shelf = false;
	newKillItem.is_available = false;
	newKillItem.source = 'killItems';

	var itemEditing = {};

	var itemList = new Vue({
		el: "body",
		data: {
			activityItems: activityItems,
			tags: tags,
			killItems: killItems,
			itemEditing: {},
			newItem: newItem,
			newKillItem: newKillItem
		},
		methods: {
			previewActivity: previewActivity,
			editItem: editItem,

			saveItem: function(item) {
				console.log(item);
				if (item.source == 'activityItems') {
					saveItem(item)
				} else {
					saveKillItem(item);
				}
			},

			validItem: validItem,
			invalidItem: invalidItem,

			putOnItem: putOnItem,
			putOffItem: putOffItem,

			shiftItem: shiftItem,
			unshiftItem: unshiftItem,

			createShortUrl: createShortUrl,

			changeOrder: changeOrder
		},
		events: {
			'save-item': function(item){
				this.saveItem(item)
			},
			'delete-item': deleteItem
		}
	});


	var qrcode = new QRCode(document.getElementById("qrcode"), {
		text: "http://www.yeyetech.net/app/wx?#activity/" + activityId,
		width: 128,
		height: 128,
		colorDark : "#000000",
		colorLight : "#ffffff",
		correctLevel : QRCode.CorrectLevel.H
	});
	function previewActivity() {
		$("#showQrcode").modal('show');
		$("#qrcode").find("img").css({margin: "0 auto"})
	}

	function editItem(item, index) {
		if (item.source == 'activityItems') {
			$("#itemEditing").modal('show');
		} else {
			$("#killItemEditing").modal('show');
		}
		this.itemEditing = $.extend(true, {}, item);
		this.itemEditing.index = index;
	}

	function saveItem(item) {
		var data = {
			activityId: activityId,
			title: item.title,
			description: item.description,
			price: item.price,
			postage: item.postage,
			marketPrice: item.market_price,
			country: item.country_id,
			seller: item.seller_id,
			editor: item.operator_id,
			limitedNumber: item.buy_per_user,
			inventory: item.inventory,
			pic_url: item.pic_urls
		};
		console.log(data);
		var key;
		var checkArr = [undefined, "", "--请选择--"];
		for (key in data) {																										//校验商品信息是否完整
			if (checkArr.indexOf(data[key]) > -1) {
				alert("请填写完整的商品信息-" + key);
				return false;
			}
		}
		if (data.pic_url.length == 0) {
			alert("请添加商品图片");
			return false
		}
		data.tag_ids = item.tag_ids;
		var url, method;
		if (item.item_id) {																										//修改商品信息保存
			data.id = item.item_id;
			url = "/operator/updateActivityItem";
			method = "get";
		} else {																															//新建商品保存
			url = "/operator/createActivityItem";
			method = "post";
			item.order = item.index;
		}
		$.ajax({
			url: url,
			type: method,
			data: data,
			success: function (res) {
				if (!item.item_id) {
					item.item_id = res;
				}
				itemList[item.source].splice(item.index, 1, item);
				console.log(item.index);
				$("#itemEditing").modal('hide');
			}
		});
	}

	function saveKillItem(item) {
		var itemData = {
			title: item.title,
			description: item.description,
			price: item.price,
			market_price: item.market_price,
			postage: 0,
			country_id: item.country_id,
			seller_id: item.seller_id,
			sku_inventory: item.inventory,
			pic_urls: item.pic_urls,
			operator_id: item.operator_id
		};
		console.log(itemData);
		var secKillData = {
			start_time: activityTime.substr(0, 10) + " " + item.start_time + ":00"
		};

		var checkArr = [undefined, "", "--请选择--"];
		for (key in itemData) {																										//校验商品信息是否完整
			if (checkArr.indexOf(itemData[key]) > -1) {
				alert("请填写完整的商品信息-" + key);
				return false;
			}
		}
		if (itemData.pic_urls.length == 0) {
			alert("请添加商品图片");
			return false
		}
		var url, method;
		if (item.item_id) {																										//修改商品信息保存
			url = "/api/secKill/" + item.id;
			method = "put";
		} else {																															//新建商品保存
			url = "/api/secKills";
			method = "post";
		}
		$.ajax({
			url: url,
			type: method,
			data: {itemData: itemData, secKillData: secKillData, activity_id: activityId},
			success: function(res) {
				if (!item.item_id) {
					item.item_id = res.item_id;
					item.id = res.secKill_id;
					itemList[item.source].push(item);
				} else {
					itemList[item.source].map(function (index) {
						if (index.item_id == item.item_id) {
							$.extend(index, item);
						}
					})
				}
				$("#killItemEditing").modal('hide');
			}
		})
	}

	function deleteItem(item) {
		var that = this;
		var url, method;
		if (item.source == "activityItems") {
			url = "/operator/deleteActivityItem/" + activityId + "/" + item.item_id;
			method = 'get'
		} else if (item.source == "killItems") {
			url = '/api/secKill/' + item.id;
			method = 'delete'
		}
		$.ajax({
			url: url,
			type: method,
			success: function (response) {
				if (response) {
					that[item.source].splice(item.index, 1);
					$("#itemEditing").modal('hide');
					$("#killItemEditing").modal('hide');
					that[item.source] = that[item.source].map(function(index, i) {
						var indexAfterParse = index;
						indexAfterParse.order = i + 1;
						return indexAfterParse;
					});
				}
			}
		});
	}

	function validItem(item) {
		if (confirm("确认恢复该商品")) {
			$.ajax({
				url: '/api/secKill/' + item.id + '/valid',
				type: 'put',
				success: function(res) {
					if (res.status_code == 200) {
						item.is_available = true;
					}
				}
			})
		}
	}

	function invalidItem(item) {
		if (confirm("确认失效该商品")) {
			$.ajax({
				url: '/api/secKill/' + item.id + '/invalid',
				type: 'put',
				success: function(res) {
					if (res.status_code == 200) {
						item.is_available = false;
						item.is_on_shelf = false;
					}
				}
			})
		}
	}


	function putOnItem(item) {
		if (item.inventory == 0) {
			alert('库存为0不能上架');
			return false
		}
		if (confirm("确认上架该商品")) {
			$.ajax({
				url: '/api/secKill/' + item.id + '/putOn',
				type: 'put',
				success: function(res) {
					if (res.status_code == 200) {
						item.is_on_shelf = true;
						item.is_available = true;
					}
				}
			})
		}
	}

	function putOffItem(item) {
		if (confirm("确认下架该商品")) {
			$.ajax({
				url: '/api/secKill/' + item.id + '/putOff',
				type: 'put',
				success: function(res) {
					if (res.status_code == 200) {
						item.is_on_shelf = false;
					}
				}
			})
		}
	}

	function shiftItem(item) {
		if (confirm("确认下架该商品？")) {
			$.get("/operator/cancelGroupItem/" + item.item_id, function(res) {
				if (res.status_code == 200) {
					item.is_on_shelf = false;
				}
			})
		}
	}
	function unshiftItem(item) {
		if (item.inventory == 0) {
			alert('库存为0不能上架');
			return false
		}
		if (confirm("确认上架该商品？")) {
			$.get("/operator/publishGroupItem/" + item.item_id, function(res) {
				if (res.status_code == 200) {
					item.is_on_shelf = true;
				}
			})
		}
	}

	var showShortUrl = $("#showShortUrl");
	function createShortUrl(item) {
		var url = activityType == 1 ? ("/app/wx?#/periodActivity??item") : ("/app/wx?#/activity/" + activityId + "??item");
		showShortUrl.find(".urlText").text(location.origin + url + item.item_id);
		showShortUrl.modal('show');
		//$.ajax({
		//	url: "/operator/itemShortUrl/" + item.item_id + "?type=" + (activityType == 1 ? 'period' : 'theme') + "&&activity_id=" + activityId,
		//	type: 'get',
		//	success: function(res) {
		//		showShortUrl.find(".urlText").text(res.shortUrl);
		//		showShortUrl.modal('show');
		//	}
		//})
	}

	function changeOrder(item, target) {
		this[item.source].$remove(item);
		this[item.source].splice(target - 1, 0, item);
		this[item.source] = this[item.source].map(function(index, i) {
			var indexAfterParse = index;
			indexAfterParse.order = i + 1;
			return indexAfterParse;
		});
	}





	//没有重构的部分, 保存活动以及编辑活动头部分
	var addOneImage = function (imageArea, newurl) {
		$("." + imageArea).find("img").attr({"src": newurl});
		$("." + imageArea).find(".showImageEx").attr({"class": "showImage"});
	};

	var addCarouselImage = function (imageArea, newurl) {
		var newCarousel = '<div class="carousel"><div class="showImage"><img src="' + newurl + '" class="pic_urls"><a class="deleteImage" name="deleteCarouselImage" href="">×</a></div><input type="text" style="width: 300px; margin-top: 82px;" placeholder="焦点图对应的链接"><div class="clearfix"></div></div>';
		$("." + imageArea).append(newCarousel);
	};


	var saveActivity = function (activity) {                            //保存活动函数
		console.log(activity);
		$.ajax({
			url: "/operator/updateActivityInfo/" + $("#activityId").text(),
			type: "get",
			data: activity,
			success: function (response) {
				alert("保存成功");
				if (publish === 1) {
					window.location = "/operator/toPublish/" + $("#activityId").text();
				}
			}
		});
	};




	$("#startTime").on("change", function (event) {
		event.preventDefault();
		function doubleNumber(num) {
			return num < 10 ? '0' + num : num;
		}
		if (activityType == 1) {
			var dateToday = new Date();
			dateToday.setTime(Date.parse($("#startTime").val()) + 86400000);
			$("#endTime").val(dateToday.getFullYear() + "-" + doubleNumber(dateToday.getMonth() + 1) + "-" + doubleNumber(dateToday.getDate()));
		}
	});

	$("#activityDetail .detail").on("change", ".chosefiles", function () {                //上传图片
		var that = this;
		var fileUploadControl = $(this)[0];
		if (fileUploadControl.files.length > 0) {
			var file = fileUploadControl.files[0];
			var name = fileUploadControl.files[0]['name'];
			var avFile = new AV.File(name, file);
			avFile.save().then(function (json) {
				var newurl = json._url;                                           //原图片地址
				var thumb = avFile.thumbnailURL(210, 210);                        //压缩图片地址
				var imageArea = $(that).attr("name");
				if (imageArea == "carouselImage") {
					addCarouselImage("carouselImage", newurl);
				} else {
					addOneImage(imageArea, newurl);
					$(that).parents(".addImage").hide();
				}
			}, function (error) {
				alert("图片存储失败");
			})
		}
	});

	$("#activityDetail .detail").on("click", ".deleteImage", function (event) {             //删除图片
		event.preventDefault();
		if ($(this).attr("name") == "deleteCarouselImage") {
			$(this).parents(".carousel").remove()
		} else {
			$(this).parents(".showImage").next("div").show();
			$(this).parents(".showImage").attr({"class": "showImageEx"});
		}
	});


	$("#saveActivity").on("click", function (event) {                       //保存活动
		event.preventDefault();
		var activity = {
			type: $(".activityType").data("type"),
			title: $(".share_title").val(),
			description: $(".share_description").val(),
			forward_url: $(".shareImage").find("img").attr("src"),
			activity_pic_url: [],
			activity_url: [],
			pic_urls: $(".activityImage").find("img").attr("src"),
			order: []
		};
		for (var i = 0; i < $(".carousel").length; i++) {
			activity.activity_pic_url.push($(".carousel").eq(i).find("img").attr("src"));
			activity.activity_url.push($(".carousel").eq(i).find("input").val());
		}
		activity.order = itemList.activityItems.map(function(index) {
			return index.item_id;
		});
		saveActivity(activity);
	});

	$("#publishActivity").on("click", function (event) {                     //发布活动
		event.preventDefault();
		if ($(".share_title").val().trim() == "" || $(".share_description").val().trim() == "" || $(".shareImage").find(".showImageEx").length == 1 || $(".activityImage").find(".showImageEx").length == 1 || itemList.activityItems.length < 2) {
			alert("请填写完整的活动信息！");
			return false
		}
		for (var i = 0; i < $(".carouselImage").find("input").length; i++) {
			if ($(".carouselImage").find("input").val().trim() == "") {
				alert("请填写完整的活动信息！");
				return false;
			}
		}
		publish = 1;
		$("#saveActivity").click();
	});

	$("#saveAndPublishActivity").on("click", function (event) {             //保存发布后的活动
		event.preventDefault();
		if ($(".share_title").val().trim() == "" || $(".share_description").val().trim() == "" || $(".shareImage").find(".showImageEx").length == 1 || $(".activityImage").find(".showImageEx").length == 1 || itemList.activityItems.length < 2) {
			alert("请填写完整的活动信息！");
			return false
		}
		for (var i = 0; i < $(".carouselImage").find("input").length; i++) {
			if ($(".carouselImage").find("input").eq(i).val().trim() == "") {
				alert("请填写完整的活动信息！");
				return false;
			}
		}
		$("#saveActivity").click();
	});


};