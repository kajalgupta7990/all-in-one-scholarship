package com.gov.scholarship.adapters;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.gov.scholarship.R;
import com.gov.scholarship.ScholarshipDetailActivity;
import com.gov.scholarship.models.Scholarship;
import java.util.List;

public class ScholarshipAdapter extends RecyclerView.Adapter<ScholarshipAdapter.ViewHolder> {
    private List<Scholarship> scholarshipList;
    private Context context;

    public ScholarshipAdapter(Context context, List<Scholarship> scholarshipList) {
        this.context = context;
        this.scholarshipList = scholarshipList;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_scholarship, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        Scholarship item = scholarshipList.get(position);
        holder.txtSchName.setText(item.getName());
        holder.txtSchCat.setText(item.getCategory());
        holder.txtSchAmt.setText(context.getString(R.string.label_benefit, item.getAmount()));
        holder.txtSchDeadline.setText(context.getString(R.string.label_deadline, item.getDeadline()));

        holder.itemView.setOnClickListener(v -> {
            Intent intent = new Intent(context, ScholarshipDetailActivity.class);
            intent.putExtra("SCHOLARSHIP_ID", item.getId());
            intent.putExtra("SCHOLARSHIP_NAME", item.getName());
            intent.putExtra("SCHOLARSHIP_CATEGORY", item.getCategory());
            intent.putExtra("SCHOLARSHIP_AMOUNT", item.getAmount());
            intent.putExtra("SCHOLARSHIP_DEADLINE", item.getDeadline());
            context.startActivity(intent);
        });
    }

    @Override
    public int getItemCount() {
        return scholarshipList.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView txtSchName, txtSchCat, txtSchAmt, txtSchDeadline;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            txtSchName = itemView.findViewById(R.id.txtSchName);
            txtSchCat = itemView.findViewById(R.id.txtSchCat);
            txtSchAmt = itemView.findViewById(R.id.txtSchAmt);
            txtSchDeadline = itemView.findViewById(R.id.txtSchDeadline);
        }
    }
}
