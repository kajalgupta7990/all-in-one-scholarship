package com.gov.scholarship.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.gov.scholarship.R;
import com.gov.scholarship.adapters.ScholarshipAdapter;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.models.Scholarship;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.List;

public class ScholarshipsFragment extends Fragment {

    private RecyclerView rv;
    private List<Scholarship> list = new ArrayList<>();
    private ScholarshipAdapter adapter;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_scholarships, container, false);

        rv = view.findViewById(R.id.rvScholarships);
        rv.setLayoutManager(new LinearLayoutManager(getActivity()));

        adapter = new ScholarshipAdapter(getActivity(), list);
        rv.setAdapter(adapter);

        loadScholarshipsFromApi();

        return view;
    }

    private void loadScholarshipsFromApi() {
        ApiClient.get("scholarships.php", new ApiClient.ApiCallback() {
            @Override
            public void onSuccess(JSONObject response) {
                JSONArray data = response.optJSONArray("data");
                if (data != null && data.length() > 0) {
                    list.clear();
                    for (int i = 0; i < data.length(); i++) {
                        JSONObject item = data.optJSONObject(i);
                        if (item != null) {
                            String id = String.valueOf(item.opt("id"));
                            String name = item.optString("name", "Scholarship Scheme");
                            String category = item.optString("category", "General");
                            String amount = item.optString("amount", "₹ 20,000 / Yr");
                            String deadline = item.optString("deadline", "Oct 31");
                            list.add(new Scholarship(id, name, category, amount, deadline));
                        }
                    }
                    adapter.notifyDataSetChanged();
                }
            }

            @Override
            public void onError(String errorMessage) {
                // Keep existing list or fallback data if offline
                if (list.isEmpty()) {
                    list.add(new Scholarship("1", "Central Sector Scheme of Scholarship", "General", "₹ 25,000 / Yr", "Oct 31"));
                    list.add(new Scholarship("2", "PG Scholarship for Single Girl Child", "General", "₹ 18,000 / Yr", "Sep 30"));
                    list.add(new Scholarship("3", "National Merit-cum-Means Scholarship Scheme", "OBC", "₹ 12,000 / Yr", "Sep 30"));
                    list.add(new Scholarship("4", "Prime Minister's Scholarship Scheme (PMSS)", "General", "₹ 30,000 / Yr", "Nov 15"));
                    list.add(new Scholarship("5", "Begum Hazrat Mahal National Scholarship", "Minority", "₹ 15,000 / Yr", "Aug 31"));
                    list.add(new Scholarship("6", "Post Matric Scholarship Scheme for SC", "SC", "₹ 25,000 / Yr", "Oct 31"));
                    adapter.notifyDataSetChanged();
                }
            }
        });
    }
}
