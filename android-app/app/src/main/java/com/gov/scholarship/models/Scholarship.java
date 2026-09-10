package com.gov.scholarship.models;

public class Scholarship {
    private String id;
    private String name;
    private String category;
    private String amount;
    private String deadline;

    public Scholarship(String id, String name, String category, String amount, String deadline) {
        this.id = id;
        this.name = name;
        this.category = category;
        this.amount = amount;
        this.deadline = deadline;
    }

    public String getId() { return id; }
    public String getName() { return name; }
    public String getCategory() { return category; }
    public String getAmount() { return amount; }
    public String getDeadline() { return deadline; }
}
