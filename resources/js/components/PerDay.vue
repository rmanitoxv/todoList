<template>
    <div className="w-full">
        <h1 className="uppercase tracking-widest text-2xl">
            {{ checkIfToday(date) ? "Today" : parseDate(date) }}
        </h1>
        <div className="flex flex-col rounded-xl w-full bg-slate-800 ">
            <TodoGroup
                v-for="todoGroup in perDay"
                :todoGroup="todoGroup"
                :getTodoLists="getTodoLists"
            />
        </div>
    </div>
</template>
<script>
import TodoGroup from "./TodoGroup.vue";
export default {
    props: {
        date: String,
        perDay: Object,
        getTodoLists: Function,
        checkIfToday: Function,
    },
    components: {
        TodoGroup,
    },
    methods: {
        parseDate(dateString) {
            const months = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ];
            const date = new Date(dateString);
            const monthIndex = date.getMonth();
            const day = date.getDate();
            const year = date.getFullYear();

            const monthName = months[monthIndex];
            const formattedDay = day < 10 ? "0" + day : day;
            return `${monthName} ${formattedDay}, ${year}`;
        },
    },
};
</script>
