package com.gov.scholarship.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.gov.scholarship.R;
import com.gov.scholarship.models.Announcement;
import java.util.List;

public class AnnouncementAdapter extends RecyclerView.Adapter<AnnouncementAdapter.ViewHolder> {
    private List<Announcement> announcementList;

    public AnnouncementAdapter(List<Announcement> announcementList) {
        this.announcementList = announcementList;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_announcement, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        Announcement item = announcementList.get(position);
        holder.txtAnnTitle.setText(item.getTitle());
        holder.txtAnnDate.setText(item.getDate());
        holder.txtAnnDesc.setText(item.getDescription());
    }

    @Override
    public int getItemCount() {
        return announcementList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView txtAnnTitle, txtAnnDate, txtAnnDesc;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            txtAnnTitle = itemView.findViewById(R.id.txtAnnTitle);
            txtAnnDate = itemView.findViewById(R.id.txtAnnDate);
            txtAnnDesc = itemView.findViewById(R.id.txtAnnDesc);
        }
    }
}
