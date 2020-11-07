<template>
    <div>
       <div class="content">
			<div class="container-fluid">

				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Comics <Button @click="$router.push('/createComic')" ><Icon type="md-add" /> Create Comic</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Title</th>
								<th>Categories</th>
								<th>Latest chapter</th>
								<th>Views</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(blog, i) in comics" :key="i" v-if="comics.length">
								<td>{{blog.id}}</td>
								<td class="_table_name">{{blog.title}}</td>
								<td> <span  v-for="(c, j) in blog.cat" v-if="blog.cat.length"><Tag type="border">{{c.categoryName}}</Tag></span> </td>

								<td><span v-for="(chapter, r) in chapters" :key="r" v-if="chapters.length && blog.id === chapter.comic_id">
								<Tag type="border">{{chapter.chapterTitle}}</Tag></span></td>



								<td> {{blog.views}}</td>

                                <td>
									<Button type="info" size="small" >View All Chapters</Button>
									<Button type="info" size="small" @click="$router.push(`/editblog/${blog.id}`)" v-if="isUpdatePermitted">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(blog, i)"  :loading="blog.isDeleting" v-if="isDeletePermitted">Delete</Button>

								</td>
							</tr>
								<!-- ITEMS -->
					</table>
					</div>
				</div>
                <deleteModal></deleteModal>

			</div>
		</div>
    </div>
</template>


<script>
import deleteModal from '../components/deleteModal.vue'
import { mapGetters } from 'vuex'
export default {
	data(){
		return {

			isAdding : false,
            index : -1,
			showDeleteModal: false,
			isDeleing : false,
			deleteItem: {},
            deletingIndex: -1,
            comics: [],
            chapters: [],
		}
	},

	methods : {
        showDeletingModal(blog, i){
            console.log('the index is ',i)
            this.deletingIndex = i
			const deleteModalObj  =  {
				showDeleteModal: true,
				deleteUrl : 'app/delete_comic',
				data : {id: blog.id},
				deletingIndex: i,
                isDeleted : false,
                msg : 'Are you sure you want to delete this comic?',
                successMsg: 'Comic has been deleted successfully!'
			}
			this.$store.commit('setDeleteModalObj', deleteModalObj)
			//console.log('delete method called')
			// this.deleteItem = tag
			// this.deletingIndex = i
			// this.showDeleteModal = true

		}
	},

	async created(){


		const [res, resChapter] = await Promise.all([
			this.callApi('get', 'app/comicsdata'), 
			this.callApi('get', 'app/latest_chapter')
		])
		if(res.status==200){
			this.comics = res.data
		}else{
			this.e()
		}
		if(resChapter.status==200){
			this.chapters = resChapter.data
		}else{
			this.e()
		}
	},
	components : {
		deleteModal
	},
	computed : {
        ...mapGetters(['getDeleteModalObj'])
    },

	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
                this.comics.splice(this.deletingIndex,1)
			}
		}
	}




}
</script>
