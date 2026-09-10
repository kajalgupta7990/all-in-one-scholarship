package com.gov.scholarship.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.gov.scholarship.*;
import com.gov.scholarship.adapters.AnnouncementAdapter;
import com.gov.scholarship.adapters.ScholarshipAdapter;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.models.Announcement;
import com.gov.scholarship.models.Scholarship;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class HomeFragment extends Fragment {

    private List<Scholarship> featuredList = new ArrayList<>();

    // Complete scholarship list for search
    private List<Scholarship> allFeaturedList = new ArrayList<>();

    private ScholarshipAdapter featAdapter;

    private List<Announcement> annList = new ArrayList<>();
    private AnnouncementAdapter annAdapter;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             @Nullable ViewGroup container,
                             @Nullable Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.fragment_home, container, false);

        // ==============================
        // Search Box
        // ==============================

        EditText etSearch = view.findViewById(R.id.etSearch);

        etSearch.setSingleLine(true);
        etSearch.setImeOptions(EditorInfo.IME_ACTION_SEARCH);

        etSearch.setOnEditorActionListener((v, actionId, event) -> {

            if (actionId == EditorInfo.IME_ACTION_SEARCH) {

                String query = etSearch.getText().toString().trim().toLowerCase();

                filterScholarships(query);

                return true;
            }

            return false;
        });

        // ==============================
        // Bind Menu cards
        // ==============================

        CardView cardChecker = view.findViewById(R.id.cardEligibility);
        CardView cardDocs = view.findViewById(R.id.cardDocuments);
        CardView cardHelp = view.findViewById(R.id.cardHelp);
        CardView cardContact = view.findViewById(R.id.cardContact);

        cardChecker.setOnClickListener(v ->
                startActivity(new Intent(getActivity(),
                        EligibilityCheckerActivity.class)));

        cardDocs.setOnClickListener(v ->
                startActivity(new Intent(getActivity(),
                        RequiredDocumentsActivity.class)));

        cardHelp.setOnClickListener(v ->
                startActivity(new Intent(getActivity(),
                        HelpSupportActivity.class)));

        cardContact.setOnClickListener(v ->
                startActivity(new Intent(getActivity(),
                        ContactActivity.class)));

        // ==============================
        // Setup featured scholarships
        // ==============================

        RecyclerView rvFeatured = view.findViewById(R.id.rvFeatured);

        rvFeatured.setLayoutManager(
                new LinearLayoutManager(
                        getActivity(),
                        LinearLayoutManager.HORIZONTAL,
                        false
                )
        );

        // Initial defaults
        featuredList.add(new Scholarship(
                "1",
                "Central Sector Scheme of Scholarship",
                "General",
                "₹ 25,000 / Yr",
                "Oct 31"
        ));

        featuredList.add(new Scholarship(
                "2",
                "PG Scholarship for Single Girl Child",
                "General",
                "₹ 18,000 / Yr",
                "Sep 30"
        ));

        featuredList.add(new Scholarship(
                "3",
                "National Merit-cum-Means Scholarship",
                "OBC",
                "₹ 12,000 / Yr",
                "Sep 30"
        ));

        // Keep complete list separately for searching
        allFeaturedList.addAll(featuredList);

        featAdapter = new ScholarshipAdapter(
                getActivity(),
                featuredList
        );

        rvFeatured.setAdapter(featAdapter);

        // ==============================
        // Setup announcements timeline
        // ==============================

        RecyclerView rvAnn = view.findViewById(R.id.rvAnnouncements);

        rvAnn.setLayoutManager(
                new LinearLayoutManager(getActivity())
        );

        annList.add(new Announcement(
                "Central Sector Deadline Extended",
                "Registration window has been extended to October 31, 2026.",
                "Aug 05"
        ));

        annList.add(new Announcement(
                "Second Installment Disbursed",
                "Aadhaar Bridge Payments worth $18.5M released to verified accounts.",
                "Jul 28"
        ));

        annAdapter = new AnnouncementAdapter(annList);

        rvAnn.setAdapter(annAdapter);

        // ==============================
        // Load Dynamic Data
        // ==============================

        loadDynamicData();

        return view;
    }

    // ==========================================================
    // SEARCH FUNCTION
    // ==========================================================

    private void filterScholarships(String query) {

        featuredList.clear();

        // If search box is empty,
        // show all scholarships again
        if (query.isEmpty()) {

            featuredList.addAll(allFeaturedList);

        } else {

            for (Scholarship scholarship : allFeaturedList) {

                String name = scholarship.getName() != null
                        ? scholarship.getName().toLowerCase()
                        : "";

                String category = scholarship.getCategory() != null
                        ? scholarship.getCategory().toLowerCase()
                        : "";

                // Search by scholarship name OR category
                if (name.contains(query) || category.contains(query)) {

                    featuredList.add(scholarship);
                }
            }
        }

        featAdapter.notifyDataSetChanged();
    }

    // ==========================================================
    // LOAD DATA FROM API
    // ==========================================================

    private void loadDynamicData() {

        // Load live featured scholarships from API
        ApiClient.get(
                "scholarships.php?limit=4",
                new ApiClient.ApiCallback() {

                    @Override
                    public void onSuccess(JSONObject response) {

                        JSONArray data = response.optJSONArray("data");

                        if (data != null && data.length() > 0) {

                            featuredList.clear();
                            allFeaturedList.clear();

                            for (int i = 0; i < data.length(); i++) {

                                JSONObject item =
                                        data.optJSONObject(i);

                                if (item != null) {

                                    Scholarship scholarship =
                                            new Scholarship(
                                                    String.valueOf(
                                                            item.opt("id")
                                                    ),
                                                    item.optString("name"),
                                                    item.optString("category"),
                                                    item.optString("amount"),
                                                    item.optString("deadline")
                                            );

                                    featuredList.add(scholarship);
                                    allFeaturedList.add(scholarship);
                                }
                            }

                            featAdapter.notifyDataSetChanged();
                        }
                    }

                    @Override
                    public void onError(String errorMessage) {

                        // Keep initial defaults
                    }
                }
        );

        // ======================================================
        // Load live announcements from API
        // ======================================================

        ApiClient.get(
                "announcements.php?limit=3",
                new ApiClient.ApiCallback() {

                    @Override
                    public void onSuccess(JSONObject response) {

                        JSONArray data =
                                response.optJSONArray("data");

                        if (data != null && data.length() > 0) {

                            annList.clear();

                            for (int i = 0; i < data.length(); i++) {

                                JSONObject item =
                                        data.optJSONObject(i);

                                if (item != null) {

                                    annList.add(
                                            new Announcement(
                                                    item.optString("title"),
                                                    item.optString("description"),
                                                    item.optString("date")
                                            )
                                    );
                                }
                            }

                            annAdapter.notifyDataSetChanged();
                        }
                    }

                    @Override
                    public void onError(String errorMessage) {

                        // Keep initial defaults
                    }
                }
        );
    }
}