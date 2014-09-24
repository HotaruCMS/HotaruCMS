//function UserManager(data) {
//    this.userCount = ko.observable(data.userCount);
//}
//
//function UserManagerViewModel() {
//    // Data
//    var self = this;
//    self.tasks = ko.observableArray([]);
//    self.newTaskText = ko.observable();
//    self.incompleteTasks = ko.computed(function() {
//        return ko.utils.arrayFilter(self.tasks(), function(task) { return !task.isDone() });
//    });
//
//    // Operations
//    self.addTask = function() {
//        self.tasks.push(new Task({ title: this.newTaskText() }));
//        self.newTaskText("");
//    };
//    self.removeTask = function(task) { self.tasks.remove(task) };
//    
//    // Load initial state from server, convert it to Task instances, then populate self.tasks
//    $.getJSON("/tasks", function(allData) {
//        var mappedTasks = $.map(allData, function(item) { return new Task(item) });
//        self.tasks(mappedTasks);
//    });    
//}
//
//ko.applyBindings(new UserManagerViewModel());