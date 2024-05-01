<template>
    <div class="bg-slate-950 text-white font-inter flex flex-col h-screen">
        <div
            :class="`fixed left-0 backdrop-brightness-75 backdrop-blur-sm h-screen w-screen transition-all top-0 ${
                isNewListOpen ? 'z-20' : '-z-10 opacity-0'
            }`"
        >
            <NewList
                :isNewListOpen="isNewListOpen"
                :changeNewList="changeNewList"
                :getTodoLists="getTodoLists"
            />
        </div>
        <div class="bg-slate-900 py-8 px-24 text-3xl font-bold">WHAT TO DO</div>
        <div
            class="flex-grow overflow-y-auto px-48 flex flex-col w-screen items-center py-12"
        >
            <div class="bg-slate-900 p-4 w-1/4 rounded-xl border-white border">
                <p>Number of tasks left for today:</p>
                <h1 class="text-8xl text-center">{{ tasks }}</h1>
            </div>
            <div class="w-full flex justify-end">
                <button :onclick="() => changeNewList(true)">
                    Add New Todo List
                </button>
            </div>
            <div className="mt-20 flex flex-col items-start w-full gap-10">
                <PerDay
                    v-for="day of groupedByDay"
                    :date="day.date"
                    :perDay="day.perDay"
                    :getTodoLists="getTodoLists"
                    :checkIfToday="checkIfToday"
                />
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import PerDay from "./components/PerDay.vue";
import NewList from "./components/NewList.vue";
export default {
    data() {
        return {
            isNewListOpen: false,
            groupedByDay: [],
            tasks: 0,
        };
    },
    methods: {
        changeNewList(value) {
            this.isNewListOpen = value;
        },
        getTodoLists() {
            this.groupedByDay = [];
            this.tasks = 0;
            axios
                .get("/api/todoGroup")
                .then((response) => {
                    for (const i in response.data) {
                        const day = this.groupedByDay.push({
                            date: i,
                            perDay: response.data[i],
                        });
                        if (this.checkIfToday(i)) {
                            for (const j of response.data[i]) {
                                this.tasks += j.todo.length;
                            }
                        }
                    }
                })
                .catch((error) => console.error(error));
        },
        checkIfToday(date) {
            const givenDate = new Date(date);
            const today = new Date();
            return givenDate.toDateString() === today.toDateString();
        },
    },
    components: {
        NewList,
        PerDay,
    },
    created() {
        this.getTodoLists();
    },
};
</script>
