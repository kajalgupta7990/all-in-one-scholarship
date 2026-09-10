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
import com.gov.scholarship.adapters.AnnouncementAdapter;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.models.Announcement;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.List;

public class NotificationsFragment extends Fragment {

    private List<Announcement> list = new ArrayList<>();
    private AnnouncementAdapter adapter;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_notifications, container, false);

        RecyclerView rv = view.findViewById(R.id.rvNotifications);
        rv.setLayoutManager(new LinearLayoutManager(getActivity()));

        adapter = new AnnouncementAdapter(list);
        rv.setAdapter(adapter);

        loadAnnouncements();

        return view;
    }

    private void loadAnnouncements() {
        ApiClient.get("announcements.php", new ApiClient.ApiCallback() {
            @Override
            public void onSuccess(JSONObject response) {
                JSONArray data = response.optJSONArray("data");
                if (data != null && data.length() > 0) {
                    list.clear();
                    for (int i = 0; i < data.length(); i++) {
                        JSONObject item = data.optJSONObject(i);
                        if (item != null) {
                            list.add(new Announcement(
                                    item.optString("title", "Notice"),
                                    item.optString("description", ""),
                                    item.optString("date", "Today")
                            ));
                        }
                    }
                    adapter.notifyDataSetChanged();
                }
            }

            @Override
            public void onError(String errorMessage) {
                if (list.isEmpty()) {
                    list.add(new Announcement("Central Sector Deadline Extended", "Registration window for Central Sector Scheme extended to October 31, 2026.", "Aug 05"));
                    list.add(new Announcement("DBT Fellowship Disbursed", "Aadhaar Bridge Payments worth $18.5M released to active bank files.", "Jul 28"));
                    list.add(new Announcement("Mandatory Nodal Officer Guidelines", "Nodal officers must update profile records with active signatures by July 31.", "Jul 15"));
                    adapter.notifyDataSetChanged();
                }
            }
        });
    }
}
