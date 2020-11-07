<template>
    <div>
       <div class="content">
			<div class="container-fluid">

				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Create chapter</p>
					<div class="_input_field">
						 <Input type="text" v-model="data.chapterTitle" placeholder="Chapter name" />
					 </div>
					
					<div class="_input_field">
						<Select  filterable placeholder="Select comic series" v-model="data.comic_id">
							<Option v-for="(c, i) in comic" :value="c.id" :key="i">{{ c.title }}</Option>
						</Select>
					</div>


					<div class="_overflow _table_div blog_editor">

                             <editor
                                ref="editor"
                                autofocus
                                holder-id="codex-editor"
                                save-button-id="save-button"
                                :init-data="initData"
                                @save="onSave"
								:config="config"
							/>


					</div>

					<div class="_input_field">
			       		<Upload
			       			ref="uploads"
					        type="drag"
					        :headers="{'x-csrf-token' : token, 'X-Requested-With' :'XMLHttpRequest'}"
					        :on-success="thumbnailSuccess"
					        :format="['jpg','jpeg','png']"
					        :max-size="2048"
					        :on-format-error="handleFormatError"
					        :on-exceeded-size="handleMaxSize"
					        action="/app/upload">
					        <div style="padding: 20px 0">
					            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
					            <p>Click or drag thumbnail image here to upload</p>
					        </div>
					    </Upload>
					    <div class="demo-upload-list" v-if="data.chapterThumbnail">
					    	<img :src="`/uploads/${data.chapterThumbnail}`" />

					    	 <div class="demo-upload-list-cover">
				               
				                <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
				            </div>
					    </div>

					</div>

					<div class="_input_field">
						<Button type="success" long to="//cdn.munla.net" target="_blank">Upload chapter zip here...</Button>
					</div>

					 <div class="_input_field">
						 <Button type="error" long @click="save" :loading="isCreating" :disabled="isCreating">{{isCreating ? 'Please wait...' : 'Create chapter'}}</Button>
					 </div>

				</div>





			</div>
		</div>
    </div>
</template>


<script>

export default {
	data(){
		return {
            config: {

			},
            initData: null,
            data: {
				chapterTitle: '',
				chapterContent : '',
				chapterThumbnail : '',
				comic_id : null,
				jsonData: null
			},
			articleHTML: '',
			comic : [],
			isCreating: false,
			token: '',
			zipFile : null,
			loadingStatus: false,


		}
	},

	methods : {

		
            async chapterUpload () {
                this.loadingStatus = true;
                const res = await this.callApi('post', '/app/chapterUpload', {mangaName: this.data.comic_id, chapterName: this.data.chapterTitle});


               
            },


        async onSave(response){
            var data = response
			await this.outputHtml(data.blocks)
			this.data.chapterContent = this.articleHTML
            this.data.jsonData = JSON.stringify(data)
            if(this.data.chapterContent.trim()=='') return this.w('Chapter data is required')
            if(this.data.chapterTitle.trim()=='') return this.w('Title is required')
            if(this.data.chapterThumbnail.trim()=='') return this.w('Thumbnail is required')
            if(this.data.comic_id == null) return this.w('Comic parents is required')

			this.isCreating = true
			const res = await this.callApi('post', 'app/create_chapter', this.data)
			if(res.status===201){
				this.s('Chapter has been created successfully!')
                // redirect...
                this.$router.push('/chapters')
			}else{
                if(res.status==422){
                    for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
                }else{
                    this.e()
                }

			}
			this.isCreating = false
        },
        async save(){
            this.$refs.editor.save()
        },
		 outputHtml(articleObj){
		   articleObj.map(obj => {
				switch (obj.type) {
				case 'paragraph':
					this.articleHTML += this.makeParagraph(obj);
					break;
				case 'image':
					this.articleHTML += this.makeImage(obj);
					break;
				case 'header':
					this.articleHTML += this.makeHeader(obj);
					break;
				case 'raw':
					this.articleHTML += `<div class="ce-block">
					<div class="ce-block__content">
					<div class="ce-code">
						<code>${obj.data.html}</code>
					</div>
					</div>
				</div>\n`;
					break;
				case 'code':
					this.articleHTML += this.makeCode(obj);
					break;
				case 'list':
					this.articleHTML += this.makeList(obj)
					break;
				case "quote":
					this.articleHTML += this.makeQuote(obj)
					break;
				case "warning":
					this.articleHTML += this.makeWarning(obj)
					break;
				case "checklist":
					this.articleHTML += this.makeChecklist(obj)
					break;
				case "embed":
					this.articleHTML += this.makeEmbed(obj)
					break;


				case 'delimeter':
					this.articleHTML += this.makeDelimeter(obj);
					break;
				default:
					return '';
				}
			});
		},
		thumbnailSuccess (res, file) {
			return this.data.chapterThumbnail = res	
	    },

	   zipChapterSuccess (res, file) {
			return this.zipFile = res	
	    },

	    handleFormatError (file) {
	        this.w('File format of ' + file.name + ' is incorrect, please select jpg or png.');
	    },
	    handleMaxSize (file) {
	        this.w('File  ' + file.name + ' is too large, no more than 2M.');
	    },
	    async deleteImage(type){
	    		const res = await this.callApi('post', '/app/delete_image', {imageName: this.data.chapterThumbnail})
		         	this.data.chapterThumbnail = ''
		         	this.$refs.uploads.clearFiles()
	
	         	if(res.status != 200){
	         		//this.data.chapterThumbnail = img
	         		this.e()
	         	}
	    },

	},
	async created(){
		this.token = window.Laravel.csrfToken;
		const [manga] = await Promise.all([
			this.callApi('get', 'app/comicsdata'),
		])
		if(manga.status==200){
			this.comic = manga.data
			
		}else{
			this.e()
		}

	}

}
</script>


<style>
	.blog_editor {
		width: 717px;
		margin-left: 160px;
		padding: 4px 7px;
		font-size: 14px;
		border: 1px solid #dcdee2;
		border-radius: 4px;
		color: #515a6e;
		background-color: #fff;
		background-image: none;
		z-index:  -1;
	}
	.blog_editor:hover {
		border: 1px solid #57a3f3;
	}

	._input_field{
		margin: 20px 0 20px 160px;
    	width: 717px;
	}
</style>
