<template>
	<div>
		<div class="content">
			<div class="container-fluid"><!--fluid-->
				
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Tags   <Button @click="addModal=true" v-if="isWritePermitted"> <Icon type="md-add" /> Add new</Button></p>

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
									
									 <Button type="info" size="small" @click="showEditModal(tag, i)" v-if="isUpdatePermitted">Edit</Button>
									 <Button type="error" size="small" @click="showDeletingModal(tag, i)" v-if="isDeletePermitted">Delete</Button>
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
		    <deleteModal></deleteModal>


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
				tagName: ''
			},
			addModal: false,
			editModal: false,
			showDeleteModal: false,
			isAdding: false,
			tags: [],
			editData: {
				tagName: ''
			},
			index: -1,
			deleteIndex : -1,
			deleteItem: {},
			isDeleting: false,
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

	showDeletingModal(tag, i){
		const deleteModalObj = {
			showDeleteModal: true,
			deleteUrl: '/app/delete_tag',
			data: tag,
			deleteIndex: i,
			isDeleted: false,
			msg : 'Are you sure you want to delete this Tag?',
                successMsg: 'Tag has been deleted successfully!'
			}
		this.$store.commit('setDeleteModalObj', deleteModalObj)
		},
	},

	async created(){
		
		const res = await this.callApi('get', '/app/get_tags');
		if(res.status == 200){
			this.tags = res.data
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
				this.tags.splice(obj.deleteIndex, 1)
			}
		}
	}
}
</script>