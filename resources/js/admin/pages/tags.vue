<template>
	<div>
		<div class="content">
			<div class="container-fluid"><!--fluid-->
				
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Tags   <Button @click="addModal=true"> <Icon type="md-add" /> Add new</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Tag name</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(tag, i) in tags" :key="i" v-if="tags.length">
								<td>{{tag.id}}</td>
								<td class="_table_name">{{tag.tagName}}</td>
								<td>{{tag.created_at}}</td>
								<td>
									
									 <Button type="info" size="small" @click="showEditModal(tag, i)">Edit</Button>
									 <Button type="error" size="small" @click="showDeletingModal(tag, i)">Delete</Button>
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
			        title="Add new tag"
					:closable="false"
			       	>
				        <Input v-model="data.tagName" placeholder="Enter tag name..."/>
				        <div slot="footer">
				        	<Button type="default" size="small" @click="addModal=false">Close</Button>
				        	<Button type="primary" size="small" @click="addTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add tag'}}</Button>
				        </div>
			    </Modal>
			    <!--TAG EDITING MODAL -->
				<Modal
			        v-model="editModal"
			        title="Edit tag"
					:closable="false"
			       	>
			        <Input v-model="editData.tagName" placeholder="Enter tag name..."/>
			        <div slot="footer">
			        	<Button type="default" size="small" @click="editModal=false">Close</Button>
			        	<Button type="primary" size="small" @click="editTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Edit tag'}}</Button>
			        </div>
			    	</Modal>

			    <!--TAG DELETING MODAL -->
			    <Modal v-model="deleteModal" width="360">
		        <p slot="header" style="color:#f60;text-align:center">
		            <Icon type="ios-information-circle"></Icon>
		            <span>Delete confirmation</span>
		        </p>
		        <div style="text-align:center">
		            <p>Are you sure you want to delete this tag?</p>
		        </div>
		        <div slot="footer">
		            <Button type="error" size="large" long :loading="showDeleteModal" @click="deleteTag">Delete</Button>
		        </div>
		    </Modal>


			</div>
		</div>
	</div>
</template>

<script>
export default{
	data(){
		return {
			data: {
				tagName: ''
			},
			addModal: false,
			editModal: false,
			deleteModal:false,
			showDeleteModal: false,
			isAdding: false,
			tags: [],
			editData: {
				tagName: ''
			},
			index: -1,
			deleteItem: {},
			i: -1,
		}
	},

	methods: {
	async addTag(){
			if (this.data.tagName.trim()=='') return this.w('Tag name is required.')
				const res = await this.callApi('post', 'app/create_tag', this.data)
			if (res.status === 201){
				this.tags.unshift(res.data)
				this.s('Tag has been added successfully!')
				this.addModal = false
				this.data.tagName = ''
			}else{
				if(res.status == 422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0]);
					}
				}else{
					this.e()
				}
				
			}
		},
		async editTag(){
			if (this.editData.tagName.trim()=='') return this.w('Tag name is required.')
				const res = await this.callApi('post', 'app/edit_tag', this.editData)
			if (res.status === 200){
				this.tags[this.index].tagName = this.editData.tagName
				this.s('Tag has been edited successfully!')
				this.editModal = false
			}else{
				if(res.status == 422){
					if(res.data.errors.tagName){
						this.e(res.data.errors.tagName[0]);
					}
				}else{
					this.e()
				}
				
			}
		},
		showEditModal(tag, index){
		let obj = {
			id: tag.id,
			tagName: tag.tagName,
		}
			this.editData = obj
			this.editModal = true
			this.index = index
		},

		async deleteTag(){
			this.showDeleteModal = true
			//this.$set(tag, 'isDeleting', true)
			const res = await this.callApi('post', '/app/delete_tag', this.deleteItem)
			if(res.status === 200){
				this.tags.splice(this.i, 1)
				this.s('Tag has been deleted successfully!')
				this.deleteModal = false
				this.showDeleteModal = false
			}else{
				this.e()
			}
		

		},

		showDeletingModal(tag, i){
			this.deleteItem = tag
			this.deleteModal = true
			this.i = i
			

		},

	},

	async created(){
		const res = await this.callApi('get', '/app/get_tags');
		if(res.status == 200){
			this.tags = res.data
		}else{
			this.e()
		}
	}
}
</script>