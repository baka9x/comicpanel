<template>
	<div>
		<div class="content">
			<div class="container-fluid"><!--fluid-->
				
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Category   <Button @click="addModal=true"> <Icon type="md-add" /> Add new</Button></p>
 			

		
					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Category name</th>
								<th>Icon image</th>
								<th>Created at</th>
								
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(category, i) in categories" :key="i" v-if="categories.length">
								<td>{{category.id}}</td>
								<td class="_table_name">{{category.categoryName}}</td>
								<td class="_table_icon_thumb">
									<img :src="`/uploads/${category.iconImage}`" />
								</td>
								<td>{{category.created_at}}</td>
								<td>
									
									 <Button type="info" size="small" @click="showEditModal(category, i)">Edit</Button>
									 <Button type="error" size="small" @click="showDeletingModal(category, i)">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->

						</table>
					</div>
				</div>
				 <Page :total="100" />
				 <!--TAG ADDING MODAL -->
				<Modal
			        v-model="addModal"
			        title="Add new category"
					:closable="false"
			       	>
			       	<Input v-model="data.categoryName" placeholder="Enter category name..."/>
			       	<div class="mt-2"></div>
			       		<Upload
			       			ref="uploads"
					        type="drag"
					        :headers="{'x-csrf-token' : token, 'X-Requested-With' :'XMLHttpRequest'}"
					        :on-success="handleSuccess"
					        :format="['jpg','jpeg','png']"
					        :max-size="2048"
					        :on-format-error="handleFormatError"
					        :on-exceeded-size="handleMaxSize"
					        action="/app/upload">
					        <div style="padding: 20px 0">
					            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
					            <p>Click or drag files here to upload</p>
					        </div>
					    </Upload>
					    <div class="demo-upload-list" v-if="data.iconImage">
					    	<img :src="`/uploads/${data.iconImage}`" />

					    	 <div class="demo-upload-list-cover">
				               
				                <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
				            </div>
					    </div>
				        
				        <div slot="footer">
				        	<Button type="default" size="small" @click="addModal=false">Close</Button>
				        	<Button type="primary" size="small" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add category'}}</Button>
				        </div>
			    </Modal>
			    <!--CATEGORY EDITING MODAL -->
				<Modal
			        v-model="editModal"
			        title="Edit category"
					:closable="false"
			       	>
			        <Input v-model="editData.categoryName" placeholder="Enter category name..."/>
			       	<div class="mt-2"></div>
			       		<Upload v-show="isIconImageNew"
			       			ref="editDataUploads"
					        type="drag"
					        :headers="{'x-csrf-token' : token, 'X-Requested-With' :'XMLHttpRequest'}"
					        :on-success="handleSuccess"
					        :format="['jpg','jpeg','png']"
					        :max-size="2048"
					        :on-format-error="handleFormatError"
					        :on-exceeded-size="handleMaxSize"
					        action="/app/upload">
					        <div style="padding: 20px 0">
					            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
					            <p>Click or drag files here to upload</p>
					        </div>
					    </Upload>
					    <div class="demo-upload-list" v-if="editData.iconImage">
					    	<img :src="`/uploads/${editData.iconImage}`" />

					    	 <div class="demo-upload-list-cover">
				               
				                <Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
				            </div>
					    </div> 
			        <div slot="footer">
			        	<Button type="default" size="small" @click="closeEditModal">Close</Button>
			        	<Button type="primary" size="small" @click="editCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Edit category'}}</Button>
			        </div>
			    	</Modal>
			    <!--CATEGORY EDITING MODAL -->
		    	<deleteModal/>

			</div>
		</div>
	</div>
</template>

<script>
import deleteModal from '../components/deleteModal'
import { mapGetters } from 'vuex'
export default{
	data(){
		return {
			data: {
				iconImage: '',
				categoryName: '',
			},
			addModal: false,
			editModal: false,
			deleteModal:false,
			showDeleteModal: false,
			isAdding: false,
			categories: [],
			editData: {
				categoryName: '',
				iconImage: '',
			},
			index: -1,
			deleteItem: {},
			deleteIndex: -1,
			isDeleting: false,
			token: '',
			isIconImageNew: false,
			isEditingItem: false,
			
		}
	},

	methods: {
	async addCategory(){
			if (this.data.categoryName.trim()=='') return this.w('Category name is required.')
			if (this.data.iconImage.trim()=='') return this.w('Icon image is required.')
				const res = await this.callApi('post', 'app/create_category', this.data)
			if (res.status === 201){
				this.categories.unshift(res.data)
				this.s('Category has been added successfully!')
				this.addModal = false
				this.data.categoryName = ''
				this.data.iconImage = ''
			}else{
				if(res.status == 422){
					if(res.data.errors.categoryName){
						this.e(res.data.errors.categoryName[0]);
					}
					if(res.data.errors.iconImage){
						this.e(res.data.errors.iconImage[0]);
					}
				}else{
					this.e()
				}
				
			}
	},
	async editCategory(){
			if (this.editData.categoryName.trim()=='') return this.w('Category name is required.')
			if (this.editData.iconImage.trim()=='') return this.w('Icon image is required.')
				const res = await this.callApi('post', 'app/edit_category', this.editData)
			if (res.status === 200){
				this.categories[this.index].categoryName = this.editData.categoryName
				this.categories[this.index].iconImage = this.editData.iconImage
				this.s('Category has been edited successfully!')
				this.editModal = false
				this.editData.categoryName = ''
				this.editData.iconImage = ''
				this.isIconImageNew = false
				this.isEditingItem = false
			}else{
				if(res.status == 422){
					if(res.editData.errors.categoryName){
						this.e(res.editData.errors.categoryName[0]);
					}
					if(res.editData.errors.iconImage){
						this.e(res.editData.errors.iconImage[0]);
					}
				}else{
					this.e()
				}
				
			}
	},
	showEditModal(category, index){
		let obj = {
			id: category.id,
			categoryName: category.categoryName,
			iconImage: category.iconImage,

		}
			this.editData = obj
			this.editModal = true
			this.index = index
			this.isEditingItem = true
	},

	showDeletingModal(category, i){
			const deleteModalObj = {
				showDeleteModal: true,
				deleteUrl: '/app/delete_category',
				data: category,
				deleteIndex: i,
				isDeleted: false,
				msg : 'Are you sure you want to delete this category?',
                successMsg: 'Category has been deleted successfully!'
			}
			this.$store.commit('setDeleteModalObj', deleteModalObj)
	},
	
	handleSuccess (res, file) {
		
			if(this.isEditingItem){
				return this.editData.iconImage = res
			}
                this.data.iconImage = res		
    },
    handleFormatError (file) {
        this.w('File format of ' + file.name + ' is incorrect, please select jpg or png.');
    },
    handleMaxSize (file) {
        this.w('File  ' + file.name + ' is too large, no more than 2M.');
    },
    async deleteImage(isAdd=true){
         	let img
         	if(!isAdd){
         		//Editing
         		this.isIconImageNew = true
	         	img = this.editData.iconImage
	         	this.editData.iconImage = ''
	         	this.$refs.editDataUploads.clearFiles()
         	}else{
         		img = this.data.iconImage
	         	this.data.iconImage = ''
	         	this.$refs.uploads.clearFiles()
         	}
         		const res = await this.callApi('post', '/app/delete_image', {imageName: img})

         	if(res.status != 200){
         		if(!isAdd){
         			this.editData.iconImage = img
         		} else{
         			this.data.iconImage = img
         		}
         		this.e()
         	}
         },
         closeEditModal(){
         	this.isEditingItem = false
         	this.editModal = false
         },
	},
	async created(){
		this.token = window.Laravel.csrfToken;
		const res = await this.callApi('get', '/app/get_categories');
		if(res.status == 200){
			this.categories = res.data
		}else{
			this.e()
		}
	},

	components: {
		deleteModal,
	},

	computed : {
        ...mapGetters(['getDeleteModalObj'])
    },

	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				this.categories.splice(obj.deleteIndex, 1)
			}
		}
	}
}
</script>