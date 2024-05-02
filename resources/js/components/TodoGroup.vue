<template>
    <form
        class="flex flex-col rounded-xl border-b border-slate-800 last:border-0"
        @submit.prevent="handleSubmit"
    >
        <div
            class="flex justify-between w-full bg-slate-900 py-6 px-12 items-center group cursor-pointer"
            @click="isTodoOpen = !isTodoOpen"
        >
            <div class="flex flex-col">
                <input
                    type="text"
                    :class="`font-bold text-2xl bg-transparent border-b outline-none ${
                        isOnEdit ? 'border-white' : 'border-transparent'
                    }`"
                    v-model="todoGroup.title"
                    :disabled="!isOnEdit"
                />
                <input
                    type="date"
                    :class="`bg-transparent border-b outline-none ${
                        isOnEdit ? 'border-white' : 'border-transparent'
                    }`"
                    v-model="todoGroup.deadline"
                    :disabled="!isOnEdit"
                />
            </div>
            <v-icon
                v-if="isTodoOpen"
                name="md-arrowdropup-round"
                class="group-hover:text-slate-950"
                scale="2"
            />
            <v-icon
                v-else
                name="md-arrowdropdown-round"
                class="group-hover:text-slate-950"
                scale="2"
            />
        </div>
        <div
            :class="`flex flex-wrap bg-slate-800 gap-4 w-full justify-between transition-all px-24 ${
                isTodoOpen && 'py-8'
            }`"
            :style="isTodoOpen ? 'height:' + todoRows : 'height: 0'"
        >
            <Todo
                v-if="isTodoOpen"
                v-for="(todo, index) in todoGroup.todo"
                :todo="todo"
                :index="index"
                :isOnEdit="isOnEdit"
                :handleRemoveTask="handleRemoveTask"
            />
            <div class="w-1/3 flex justify-center items-center">
                <button
                    type="button"
                    :class="`py-2 px-4 text-xs flex gap-2 items-center bg-slate-900 ${
                        !isOnEdit && 'opacity-0'
                    }`"
                    :onclick="
                        () => {
                            todoGroup.todo.push({
                                todo_group_id: todoGroup.id,
                                name: '',
                                is_done: false,
                            });
                            todoRows = `${
                                (Math.floor(this.todoGroup.todo.length / 2) +
                                    1) *
                                5
                            }rem`;
                        }
                    "
                >
                    Add new task <v-icon name="co-plus" />
                </button>
            </div>
            <div
                v-if="isTodoOpen"
                :class="`flex w-full overflow-y-auto gap-2 cursor-pointer ${
                    !todoGroup.file.length && 'justify-center'
                }`"
            >
                <input
                    type="file"
                    id="newFile"
                    class="hidden"
                    :onchange="handleFileUpload"
                />
                <div
                    v-for="(file, index) in todoGroup.file"
                    class="flex w-36 border rounded-xl items-center px-1"
                >
                    <p class="truncate">{{ file.file_name }}</p>
                    <button
                        v-if="file.id"
                        type="button"
                        class="border-none p-0 hover:text-green-500 hover:bg-transparent bg-transparent flex items-center"
                        @click="downloadFile(file.file_path, file.file_name)"
                    >
                        <v-icon name="hi-solid-download" />
                    </button>
                    <button
                        type="button"
                        :class="`border-none p-0 hover:text-red-500 hover:bg-transparent bg-transparent flex items-center ${
                            !isOnEdit && 'hidden'
                        }`"
                        @click="() => handleRemoveFile(index, file.id)"
                    >
                        <v-icon name="bi-x-lg" />
                    </button>
                </div>
                <label
                    v-if="isOnEdit"
                    for="newFile"
                    class="border px-2 py-1 rounded-xl cursor-pointer hover:bg-white hover:text-slate-900 flex items-center"
                >
                    <v-icon v-if="todoGroup.file.length" name="co-plus" />
                    <p v-else>Upload File</p>
                </label>
            </div>
        </div>
        <div
            v-if="isOnEdit"
            class="flex justify-end w-full bg-slate-900 gap-4 px-12 py-4"
        >
            <button
                type="button"
                class="bg-red-500 hover:text-red-500"
                @click="
                    () => {
                        getTodoLists();
                    }
                "
            >
                Cancel
            </button>
            <button class="bg-green-500 hover:text-green-500">Save</button>
        </div>
        <div
            v-else
            class="flex justify-end w-full bg-slate-900 gap-4 px-12 py-4"
        >
            <button
                type="button"
                class="bg-red-500 hover:text-red-500"
                @click="handleDelete"
            >
                Delete
            </button>
            <button
                type="button"
                class="bg-green-500 hover:text-green-500"
                @click="() => (isOnEdit = true)"
            >
                Edit
            </button>
        </div>
    </form>
</template>
<script>
import Todo from "./Todo.vue";
export default {
    props: {
        todoGroup: Object,
        getTodoLists: Function,
    },
    components: {
        Todo,
    },
    data() {
        return {
            isOnEdit: false,
            isTodoOpen: false,
            todoRows: `${
                (Math.floor(this.todoGroup.todo.length / 2) + 1) * 5 + 2
            }rem`,
            deletedTasks: [],
            deletedFiles: [],
            files: [],
        };
    },
    methods: {
        handleFileUpload(event) {
            this.files.push(event.target.files[0]);
            this.$props.todoGroup.file.push({
                file_name: event.target.files[0].name,
            });
        },
        async handleSubmit() {
            await axios.patch("/api/todoGroup/" + this.$props.todoGroup.id, {
                title: this.$props.todoGroup.title,
                deadline: this.$props.todoGroup.deadline,
                todo: this.$props.todoGroup.todo.filter((item) => {
                    return item.id;
                }),
            });
            for (const todo of this.$props.todoGroup.todo) {
                if (!todo.id && todo.name.length) {
                    await axios.post("/api/todo", {
                        todo: this.$props.todoGroup.todo.filter((item) => {
                            return !item.id && item.name.length;
                        }),
                    });
                    break;
                }
            }
            if (this.deletedTasks.length) {
                await axios.post("/api/deleteTodo", {
                    ids: this.deletedTasks,
                });
            }
            if (this.deletedFiles.length) {
                await axios.post("/api/deleteFile", {
                    ids: this.deletedFiles,
                });
            }
            if (this.files.length) {
                const formData = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    formData.append("files[]", this.files[i]);
                }
                formData.append("todo_group_id", this.$props.todoGroup.id);
                await axios.post("/api/file", formData);
            }
            this.$props.getTodoLists();
        },
        handleRemoveTask(index, id) {
            if (id) this.deletedTasks.push(id);
            this.$props.todoGroup.todo.splice(index, 1);
        },
        handleRemoveFile(index, id) {
            if (id) this.deletedFiles.push(id);
            this.$props.todoGroup.file.splice(index, 1);
        },
        handleDelete() {
            axios
                .delete("/api/todoGroup/" + this.$props.todoGroup.id)
                .then(() => this.$props.getTodoLists())
                .catch((error) => console.error(error));
        },
        downloadFile(filePath, fileName) {
            axios
                .get(`/api/download-file/${filePath}`, {
                    responseType: "blob",
                })
                .then((response) => {
                    const url = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", fileName); // set desired file name
                    document.body.appendChild(link);
                    link.click();
                })
                .catch((error) => console.error(error));
        },
    },
    watch: {
        "todoGroup.todo": {
            handler: function (newValue, oldValue) {
                this.todoRows = `${
                    (Math.floor(newValue.length / 2) + 1) * 5 + 2
                }rem`;
            },
            deep: true,
        },
    },
};
</script>
