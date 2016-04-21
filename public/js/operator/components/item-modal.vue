<style>
	.modal-dialog {
		width: 600px
	}
</style>

<template id="item-template">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header" style="text-align: right; padding: 5px 15px">
				<button @click="saveItem" type="button" class="btn btn-primary save">保存</button>
				<span class="placeholder">0</span>
				<button @click="deleteItem" type="button" class="btn btn-danger deleteItem">删除</button>
				<span class="placeholder">0</span>
				<button type="button" class="btn btn-danger closeM" data-dismiss="modal" aria-hidden="true">关闭
				</button>
			</div>

			<div class="modal-body">
				<form role="form" method="post" class="clearfix form-horizontal" action="">
					<div v-if="type=='activityItem'" class="dropdown" id="tagSelect">
						<button type="button" class="btn btn-xs dropdown-toggle" id="dropdownMenu1"
						        data-toggle="dropdown">
							添加标签
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
							<li v-for="tag in tags">
								<span class="badge" role="menuitem" tabindex="-1"
								      @click="addTag(tag)" :style="tag.style" v-html="tag.tag_name"></span>
							</li>
						</ul>
						<span class="badge" v-for="tag in item.tags" track-by="$index"
						      @click="deleteTag(tag)" :style="tag.style" v-html="tag.tag_name"></span>
					</div>
					<label>商品名称</label>
					<input v-model="item.title" v-sub-str="32" type="text" name="itemTitle" class="form-control itemTitle"
					       placeholder="不超过16个字"/>
					<label>商品介绍</label>
					<textarea v-model="item.description" name="itemDescription"
					          class="itemDescription form-control"
					          placeholder="填写商品介绍"></textarea>
					<div v-if="type == 'killItem'">
						<label>开始时间</label>
						<input v-model="item.start_time" type="time" class="form-control">
					</div>
					<div class="col-lg-12 col-md-12" style="padding: 0">
						<div class="col-lg-4 col-md-4" style="padding-left:0">
							<label>价格</label>

							<div class="input-group">
								<input v-model="item.price" v-min="0.01" type="number" name="itemPrice" class="form-control">
								<span class="input-group-addon">RMB</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<label>邮费</label>

							<div class="input-group">
								<input v-model="item.postage" v-min="0" type="number" name="postage"
								       class="form-control">
								<span class="input-group-addon">RMB</span>
							</div>
						</div>
						<div class="col-lg-4 col-md-4" style="padding-right: 0">
							<label>市场价</label>

							<div class="input-group">
								<input v-model="item.market_price" v-min="0.01" type="number" name="marketPrice"
								       class="form-control">
								<span class="input-group-addon">RMB</span>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12" style="padding: 0">
						<div class="col-lg-6 col-md-6" style="padding-left:0">
							<label>买手国家</label>

							<div class="input-group col-lg-12 col-md-12">
								<select class="form-control sellerCountry" name="sellerCountry"
								        @change="loadSeller" v-model="item.country_id">
									<option>--请选择--</option>
									<option v-for="country in countries" :value="country.country_id">{{country.name}}</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-6" style="padding: 0">
							<label>买手</label>

							<div class="input-group col-lg-12 col-md-12">
								<select class="form-control seller" name="seller"
									v-model="item.seller_id">
									<option>--请选择--</option>
									<option v-for="seller in sellers" :value="seller.seller_id">{{seller.name}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6" style="padding-left: 0">
						<label>处理人</label>
						<select name="editor" class="form-control editor"
							v-model="item.operator_id">
							<option v-for="operator in employees" :value="operator.employee_id">{{operator.real_name}}</option>
						</select>
					</div>
					<div class="col-lg-12 col-md-12" style="padding: 0">
						<div v-if="type == 'activityItem'" class="col-lg-6 col-md-6" style="padding-left:0">
							<label>限购</label>

							<div class="input-group">
								<input type="number" name="limitedNumber" class="form-control limitedNumber"
								       v-model="item.buy_per_user" v-min="1">
								<span class="input-group-addon">件</span>
							</div>
						</div>
						<div class="col-lg-6 col-md-6" style="padding: 0">
							<label>库存</label>

							<div v-if="type == 'killItem'" class="input-group">
								<input type="number" name="inventory" class="form-control inventory"
								       v-model="item.inventory" v-min="1" v-max="10">
								<span class="input-group-addon">件</span>
							</div>
							<div v-else class="input-group">
								<input type="number" name="inventory" class="form-control inventory"
								       v-model="item.inventory" v-min="1">
								<span class="input-group-addon">件</span>
							</div>
						</div>
					</div>
					<div class="itemImage">
						<div class="showImage" v-for="pic_url in item.pic_urls">
							<img :src="pic_url" class="pic_urls">
							<a @click.prevent="deleteImage(pic_url)" class="deleteImage" href="">×</a>
						</div>
						<div class="addImage" v-show="item.pic_urls.length == 0">
							<input v-el:avatar @change="uploadImage" type="file" class="chosefiles" name="itemImage" v-model="pic_url">
							<span class="addicon">+</span>
						</div>
						<div class="clearfix"></div>
					</div>
					<p>商品图片长宽比为1:1（大小不要超过30kb）</p>
				</form>
			</div>
		</div>
	</div>
</template>

<script>
	Vue.component('item-modal', {
		template: "#item-template",
		props: ['type', 'item', 'countries', 'employees', 'tags'],
		data: function () {
			return {
				sellers: []
			}
		},
		methods: {
			loadSeller: function() {
				var that = this;
				if (that.item.country_id) {
					$.get("/operator/getBuyer/" + that.item.country_id, function (res) {
						that.sellers = res[0].map(function (index, i) {
							return {seller_id: index, name: res[1][i]}
						});
					});
				}
			},
			uploadImage: function(ev) {
				ev.preventDefault();
				var that = this;
				var fileUploadControl = this.$els.avatar;
				if (fileUploadControl.files.length > 0) {
					var file = fileUploadControl.files[0];
					var name = fileUploadControl.files[0]['name'];
					var avFile = new AV.File(name, file);
					avFile.save().then(function (json) {
						var newUrl = json._url;                                           //原图片地址
						//						var thumb = avFile.thumbnailURL(210, 210);                        //压缩图片地址
						that.item.pic_urls.push(newUrl);
					}, function (error) {
						alert("图片存储失败" + error);
					})
				}
			},
			deleteImage: function(pic) {
				this.item.pic_urls.$remove(pic);
			},
			addTag: function(tag) {
				this.item.tags.push(tag);
				this.item.tag_ids.push(tag.id.toString());
			},
			deleteTag: function(tag) {
				this.item.tags.$remove(tag);
				this.item.tag_ids.$remove(tag.id.toString());
			},
			saveItem: function() {
				this.$dispatch('save-item', this.item);
			},
			deleteItem: function() {
				this.$dispatch('delete-item', this.item);

			}
		},
		watch: {
			'item': function(value) {
				this.loadSeller();
			}
		}
	});
</script>