<template>
    <div>
       <div class="content">
			<div class="container-fluid">

				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Chapters <Button @click="$router.push('/createChapter')" ><Icon type="md-add" /> Create Chapter</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Title</th>
								<th>Parent comic</th>
								
								<th>Views</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(chapter, i) in chapters" :key="i" v-if="chapters.length">
								<td>{{chapter.id}}</td>
								<td class="_table_name">{{chapter.chapterTitle}}</td>
								<td><span v-for="(comic, j) in comics" :key="j" v-if="comics.length && chapter.comic_id === comic.id">
								<Tag type="border">{{comic.title}}</Tag></span></td>
								<td> {{chapter.chapterViews}}</td>

                                <td>
									
									<Button type="info" size="small" @click="$router.push(`/editchapter/${chapter.id}`)" v-if="isUpdatePermitted">Edit</Button>
									<Button type="error" size="small" @click="showDeletingModal(chapter, i)"  :loading="chapter.isDeleting" v-if="isDeletePermitted">Delete</Button>

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
            chapters: [],
            comics : [],
		}
	},

	methods : {
        showDeletingModal(chapter, i){
            console.log('the index is ',i)
            this.deletingIndex = i
			const deleteModalObj  =  {
				showDeleteModal: true,
				deleteUrl : 'app/delete_chapter',
				data : {id: chapter.id},
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
		const [res, resComic] = await Promise.all([
			this.callApi('get', 'app/get_chapters'), 
			this.callApi('get', 'app/comicsdata')
		])
		if(res.status==200){
			this.chapters = res.data
		}else{
			this.e()
		}
		if(resComic.status==200){
			this.comics = resComic.data
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
                this.chapters.splice(this.deletingIndex,1)
			}
		}
	}




}
</script>
