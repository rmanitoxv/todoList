<template>
    <div
        :class="`fixed z-20 top-[10%] left-1/2 -translate-x-1/2 rounded-xl bg-slate-900 p-6 transition-all overflow-hidden select-none ${
            isNewListOpen ? 'w-[35rem] h-[36rem]' : 'w-0 h-0'
        }`"
    >
        <form
            @submit.prevent="handleSubmit"
            ref="formRef"
            class="flex flex-col justify-center items-center gap-4 h-full"
        >
            <h1 className="font-bold text-xl">NEW TODO LIST</h1>
            <input
                type="text"
                class="text-2xl text-center bg-transparent border-b border-white outline-none"
                placeholder="Title"
                v-model="title"
                required
                maxlength="16"
            />
            <div class="flex gap-4">
                <label for="deadline"> Deadline: </label>
                <input
                    type="date"
                    id="deadline"
                    v-model="deadline"
                    class="bg-transparent outline-none border-b border-white"
                    required
                    :min="new Date().toLocaleDateString('en-CA')"
                />
            </div>
            <div
                class="overflow-y-auto flex-grow flex flex-col items-center gap-4 w-full px-12"
            >
                <div
                    v-for="(i, index) in todoList"
                    class="flex w-full justify-between text-lg"
                >
                    <div class="flex gap-4">
                        <label for="name">Name</label>
                        <input
                            type="text"
                            id="name"
                            v-model="todoList[index]"
                            class="bg-slate-950 text-white outline-none"
                            required
                            maxlength="64"
                        />
                    </div>
                    <button
                        type="button"
                        class="border-none p-0 hover:text-red-500 hover:bg-transparent"
                        :onclick="() => todoList.splice(index, 1)"
                    >
                        <v-icon name="bi-x-lg" />
                    </button>
                </div>
                <div class="w-full flex justify-end">
                    <button
                        type="button"
                        class="py-1 px-2 text-xs flex gap-2 h-full items-center"
                        :onclick="
                            () => {
                                todoList.push('');
                            }
                        "
                    >
                        Add new task <v-icon name="co-plus" />
                    </button>
                </div>
            </div>
            <div
                :class="`flex w-full overflow-y-auto gap-2 ${
                    !files.length && 'justify-center'
                }`"
            >
                <input
                    type="file"
                    id="file"
                    class="hidden"
                    @change="handleFileUpload"
                />
                <div
                    v-for="(file, index) in files"
                    class="flex w-36 border rounded-xl items-center h-full px-1"
                >
                    <p class="truncate">{{ file.name }}</p>
                    <button
                        type="button"
                        class="border-none p-0 hover:text-red-500 hover:bg-transparent flex items-center"
                        :onclick="() => files.splice(index, 1)"
                    >
                        <v-icon name="bi-x-lg" />
                    </button>
                </div>
                <label
                    for="file"
                    class="border px-2 py-1 rounded-xl cursor-pointer hover:bg-white hover:text-slate-900 flex items-center"
                >
                    <v-icon v-if="files.length" name="co-plus" />
                    <p v-else>Upload File</p>
                </label>
            </div>
            <p class="text-red-500 text-lg">{{ error }}</p>
            <div
                class="border-t border-slate-500 flex w-full justify-between py-2"
            >
                <button type="button" :onclick="handleClose">CANCEL</button>
                <button>ADD</button>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    name: "NewList",
    props: {
        isNewListOpen: Boolean,
        changeNewList: Function,
        getTodoLists: Function,
    },
    data() {
        return {
            todoList: ["", ""],
            files: [],
            deadline: "",
            title: "",
            error: "",
        };
    },
    methods: {
        handleClose() {
            this.$refs.formRef.reset();
            this.todoList = ["", ""];
            this.files = [];
            this.error = "";
            this.deadline = "";
            this.title = "";
            this.changeNewList(false);
        },
        handleFileUpload(event) {
            this.files.push(event.target.files[0]);
            console.log(this.files);
        },
        handleSubmit() {
            this.error = "";
            const formData = new FormData();
            if (!this.todoList.length) {
                return (this.error = "At least one task is required.");
            }
            for (let i = 0; i < this.files.length; i++) {
                formData.append("files[]", this.files[i]);
            }
            for (let i = 0; i < this.todoList.length; i++) {
                formData.append("todo[]", this.todoList[i]);
            }
            formData.append("title", this.title);
            formData.append("deadline", this.deadline);
            axios
                .post("/api/todoGroup", formData)
                .then(() => {
                    this.$refs.formRef.reset();
                    this.todoList = ["", ""];
                    this.files = [];
                    this.error = "";
                    this.deadline = "";
                    this.title = "";
                    this.$props.getTodoLists();
                    this.$props.changeNewList(false);
                })
                .catch((error) => console.error(error));
        },
    },
};
</script>
